<?php
namespace WebGuyJeff\Error_Monitor;

use WP_REST_Request;

/**
 * Handle REST actions for the plugin.
 */
class Action_Controller {

	/**
	 * Handle incoming REST request.
	 *
	 * @param WP_REST_Request $request Request object.
	 */
	public function handle( WP_REST_Request $request ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			HTTP_Response::send_json( array( 403, 'Unauthorized request.' ) );
			exit;
		}

		$data = json_decode( $request->get_body(), true );

		if ( ! is_array( $data ) || empty( $data['action'] ) ) {
			HTTP_Response::send_json( array( 400, 'Invalid request.' ) );
			exit;
		}

		$action  = sanitize_text_field( $data['action'] );
		$payload = isset( $data['payload'] ) && is_array( $data['payload'] ) ? $data['payload'] : array();

		switch ( $action ) {

			case 'update_setting':
				$this->update_setting( $payload );
				break;

			case 'manual_scan':
				$this->manual_scan();
				break;

			case 'discover_log':
				$this->discover_log();
				break;

			case 'apply_debug':
				$this->apply_debug( $payload );
				break;

			case 'fetch_logs':
				$this->fetch_logs( $payload );
				break;

			case 'test':
				$this->test( $payload );
				break;

			default:
				HTTP_Response::send_json( array( 400, 'Unknown action.' ) );
				exit;
		}
	}

	/**
	 * Update a single setting.
	 */
	private function update_setting( array $payload ) {

		$key   = sanitize_text_field( $payload['key'] ?? '' );
		$value = $payload['value'] ?? null;

		if ( ! $key ) {
			HTTP_Response::send_json( array( 400, 'Invalid setting key.' ) );
			exit;
		}

		$result = Settings::set( $key, $value );

		if ( ! $result ) {
			HTTP_Response::send_json(
				array( 400, 'Invalid value' ),
				array( 'field' => $key )
			);
			exit;
		}

		HTTP_Response::send_json( array( 200, 'Setting updated.' ) );
		exit;
	}

	/**
	 * Run manual scan.
	 */
	private function manual_scan() {

		$controller = new Scan_Controller();
		$result     = $controller->run( true );

		HTTP_Response::send_json(
			array( 200, $result['message'] ),
			$result
		);
		exit;
	}

	/**
	 * Auto discover log file.
	 */
	private function discover_log() {

		$discovery = new Log_File_Discovery();
		$found     = $discovery->auto_discover();

		if ( $found ) {
			Settings::set( 'log_file_path', $found );
			HTTP_Response::send_json( array( 200, 'Log file auto-discovered and saved.' ) );
		} else {
			HTTP_Response::send_json( array( 404, 'No readable log file found.' ) );
		}

		exit;
	}

	/**
	 * Apply debug settings to wp-config.php.
	 */
	private function apply_debug( array $payload ) {

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
			HTTP_Response::send_json( array( 200, 'Debug settings updated.' ) );
		} else {
			HTTP_Response::send_json( array( 500, 'Failed to update debug settings.' ) );
		}

		exit;
	}

	/**
	 * Fetch logs.
	 */
	private function fetch_logs( array $payload ) {

		$repo    = new Log_Repository();
		$entries = $repo->get_recent_logs( 300 );

		$view = isset( $payload['view'] ) ? sanitize_text_field( $payload['view'] ) : 'grouped';

		ob_start();

		if ( 'raw' === $view ) {
			echo '<pre>' . esc_html( implode( "\n", array_column( $entries, 'raw' ) ) ) . '</pre>';
		} else {
			echo Log_Renderer_Email_Safe::render( $entries );
		}

		$html = ob_get_clean();

		HTTP_Response::send_json(
			array( 200, 'Logs loaded.' ),
			array( 'html' => $html )
		);

		exit;
	}

	/**
	 * Run test actions (smtp/email).
	 */
	private function test( array $payload ) {

		$type       = sanitize_text_field( $payload['type'] ?? '' );
		$controller = new Test_Controller();
		$request    = array( 'test' => $type );

		$result = $controller->perform_test( $request );

		HTTP_Response::send_json( $result );
		exit;
	}
}
