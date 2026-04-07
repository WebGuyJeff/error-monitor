<?php
namespace WebGuyJeff\Error_Monitor;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Structured REST API controller for admin SPA endpoints.
 */
class Action_Controller {

	/**
	 * Permission callback for admin API routes.
	 */
	public function can_manage_options(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Build consistent JSON response for SPA.
	 */
	private function response( int $status, $output, array $data = array() ): WP_REST_Response {
		$messages = is_array( $output ) ? $output : array( (string) $output );
		return new WP_REST_Response(
			array(
				'ok'     => $status < 300,
				'output' => $messages,
				'data'   => $data,
			),
			$status
		);
	}

	/**
	 * Parse JSON request body.
	 */
	private function get_payload( WP_REST_Request $request ): array {
		$payload = $request->get_json_params();
		return is_array( $payload ) ? $payload : array();
	}

	/**
	 * Bootstrap data for SPA shell.
	 */
	public function bootstrap(): WP_REST_Response {
		$settings       = Settings::get();
		$last_scan_time = isset( $settings['last_scan_time'] ) ? (int) $settings['last_scan_time'] : 0;
		$last_log_time  = isset( $settings['last_log_timestamp'] ) ? (int) $settings['last_log_timestamp'] : 0;
		$discovery      = new Log_File_Discovery();

		return $this->response(
			200,
			'Bootstrap loaded.',
			array(
				'pluginName'    => 'Error Monitor',
				'settings'      => is_array( $settings ) ? $settings : array(),
				'status'        => array(
					'emailConfigured' => Settings::email_configured( $settings ),
					'cronScheduled'   => Cron_Service::cron_scheduled(),
					'lastScan'        => $last_scan_time ? Timestamp::unix_to_readable( $last_scan_time ) : '',
					'lastLog'         => $last_log_time ? Timestamp::unix_to_readable( $last_log_time ) : '',
				),
				'tabs'          => array(
					array(
						'slug'  => 'monitor',
						'title' => 'Monitor',
					),
					array(
						'slug'  => 'logs',
						'title' => 'Logs',
					),
					array(
						'slug'  => 'email',
						'title' => 'Email Account',
					),
					array(
						'slug'  => 'log-file',
						'title' => 'Log File',
					),
				),
				'logFileStatus' => $discovery->get_status(),
			)
		);
	}

	/**
	 * Update a single setting.
	 */
	public function update_setting( WP_REST_Request $request ): WP_REST_Response {
		$payload = $this->get_payload( $request );
		$key     = sanitize_text_field( $payload['key'] ?? '' );
		$value   = $payload['value'] ?? null;

		if ( ! $key ) {
			return $this->response( 400, 'Invalid setting key.' );
		}

		$result = Settings::set( $key, $value );

		if ( ! $result ) {
			return $this->response( 400, 'Invalid value', array( 'field' => $key ) );
		}

		return $this->response( 200, 'Setting updated.' );
	}

	/**
	 * Run manual scan.
	 */
	public function manual_scan(): WP_REST_Response {
		$controller = new Scan_Controller();
		$result     = $controller->run( true );
		$status     = $result['success'] ? 200 : 500;

		return $this->response( $status, $result['message'], $result );
	}

	/**
	 * Auto discover log file.
	 */
	public function discover_log(): WP_REST_Response {
		$discovery = new Log_File_Discovery();
		$found     = $discovery->auto_discover();

		if ( $found ) {
			Settings::set( 'log_file_path', $found );
			return $this->response( 200, 'Log file auto-discovered and saved.' );
		}

		return $this->response( 404, 'No readable log file found.' );
	}

	/**
	 * Apply debug settings to wp-config.php.
	 */
	public function apply_debug( WP_REST_Request $request ): WP_REST_Response {
		$payload   = $this->get_payload( $request );
		$constants = array(
			'wp_debug',
			'wp_debug_log',
			'wp_debug_display',
		);

		$const = array();
		foreach ( $constants as $constant ) {
			if ( array_key_exists( $constant, $payload ) ) {
				$const = array( $constant => $payload[ $constant ] );
			}
		}

		$editor = new WP_Config_Editor();
		$result = $editor->update( $const );

		if ( $result ) {
			return $this->response( 200, 'Debug settings updated.' );
		}

		return $this->response( 500, 'Failed to update debug settings.' );
	}

	/**
	 * Fetch logs in requested format.
	 */
	public function fetch_logs( WP_REST_Request $request ): WP_REST_Response {
		$repo    = new Log_Repository();
		$entries = $repo->get_recent_logs( 300 );
		$view    = sanitize_text_field( $request->get_param( 'view' ) ?: 'grouped' );

		ob_start();
		if ( 'raw' === $view ) {
			echo '<pre>' . esc_html( implode( "\n", array_column( $entries, 'raw' ) ) ) . '</pre>';
		} else {
			echo wp_kses_post( Log_Renderer_Email_Safe::render( $entries ) );
		}
		$html = (string) ob_get_clean();

		return $this->response( 200, 'Logs loaded.', array( 'html' => $html ) );
	}

	/**
	 * Get current log file status.
	 */
	public function log_file_status(): WP_REST_Response {
		$discovery = new Log_File_Discovery();
		$status    = $discovery->get_status();

		return $this->response( 200, 'Log file status loaded.', $status );
	}

	/**
	 * Run SMTP/email test actions.
	 */
	public function test( WP_REST_Request $request ): WP_REST_Response {
		$payload    = $this->get_payload( $request );
		$type       = sanitize_text_field( $payload['type'] ?? '' );
		$controller = new Test_Controller();
		$result     = $controller->perform_test( array( 'test' => $type ) );

		$status = isset( $result[0] ) ? (int) $result[0] : 500;
		$output = isset( $result[1] ) ? $result[1] : 'Unknown response.';

		return $this->response( $status, $output );
	}
}
