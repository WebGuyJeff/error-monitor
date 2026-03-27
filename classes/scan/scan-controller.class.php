<?php

namespace WebGuyJeff\Error_Monitor;

class Scan_Controller {

	/**
	 * Run scan process.
	 *
	 * @param bool $manual Whether triggered manually (admin UI).
	 * @return array
	 */
	public function run( bool $manual = false ): array {

		$result = array(
			'success' => false,
			'message' => '',
			'count'   => 0,
			'logs'    => '',
		);

		$scanner = new Log_Scanner();

		try {

			$scanner->scan();

			$result['count'] = $scanner->count;
			$result['logs']  = $scanner->logs;

			// Detect failure: no logs AND scanner couldn't run properly
			// (basic heuristic: no log file or unreadable → scanner does nothing)
			if ( $scanner->logs === '' && $scanner->count === 0 ) {

				// Could be valid (no new logs) OR failure
				// We treat as success but no logs
				$result['success'] = true;
				$result['message'] = __( 'Scan completed. No new logs found.', 'error-monitor' );

				return $result;
			}

			// Logs found → success
			$result['success'] = true;
			$result['message'] = sprintf(
				/* translators: %d = number of logs */
				__( 'Scan completed. %d new log(s) found.', 'error-monitor' ),
				$scanner->count
			);

			// Send success email
			$this->send_success_email( $scanner );

		} catch ( \Throwable $e ) {

			error_log( 'Error_Monitor Scan Failure: ' . $e->getMessage() );

			$result['success'] = false;
			$result['message'] = __( 'Scan failed. Check logs for details.', 'error-monitor' );

			// Send failure email
			$this->send_failure_email( $e->getMessage() );
		}

		return $result;
	}

	/**
	 * Send success email with logs.
	 *
	 * @param Log_Scanner $scanner Scanner instance.
	 * @return void
	 */
	private function send_success_email( Log_Scanner $scanner ): void {

		$settings = Settings::get();

		if ( ! Settings::email_configured( $settings ) ) {
			return;
		}

		$mailer = new Mail_SMTP();

		$subject = sprintf(
			'[%s] %d New PHP Error(s) Detected',
			get_bloginfo( 'name' ),
			$scanner->count
		);

		$plaintext = $scanner->logs;

		$html = nl2br( esc_html( $scanner->logs ) );

		$mailer->send(
			$settings['host'],
			$settings['port'],
			$settings['username'],
			$settings['password'],
			$settings['to_email'],
			$settings['from_email'],
			get_bloginfo( 'name' ),
			get_bloginfo( 'name' ),
			$settings['from_email'],
			$subject,
			$html,
			$plaintext,
			parse_url( home_url(), PHP_URL_HOST )
		);
	}

	/**
	 * Send failure email.
	 *
	 * @param string $error Error message.
	 * @return void
	 */
	private function send_failure_email( string $error ): void {

		$settings = Settings::get();

		if ( ! Settings::email_configured( $settings ) ) {
			return;
		}

		$mailer = new Mail_SMTP();

		$subject = sprintf(
			'[%s] Error Monitor Scan Failed',
			get_bloginfo( 'name' )
		);

		$plaintext = "Scan failed:\n\n" . $error;

		$html = '<p><strong>Scan failed:</strong></p><pre>' . esc_html( $error ) . '</pre>';

		$mailer->send(
			$settings['host'],
			$settings['port'],
			$settings['username'],
			$settings['password'],
			$settings['to_email'],
			$settings['from_email'],
			get_bloginfo( 'name' ),
			get_bloginfo( 'name' ),
			$settings['from_email'],
			$subject,
			$html,
			$plaintext,
			parse_url( home_url(), PHP_URL_HOST )
		);
	}
}