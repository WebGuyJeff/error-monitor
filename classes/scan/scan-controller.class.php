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

		// block cron scans when disabled, allow manual.
		if ( ! $manual && ! Settings::get_or_default( 'monitor_enabled', true ) ) {
			return array(
				'success' => true,
				'message' => __( 'Monitoring is disabled. Scheduled scan skipped.', 'error-monitor' ),
				'total'   => 0,
				'unique'  => 0,
				'entries' => array(),
				'first'   => null,
				'last'    => null,
			);
		}

		$result = array(
			'success' => false,
			'message' => '',
			'total'   => 0,
			'unique'  => 0,
			'entries' => array(),
			'first'   => null,
			'last'    => null,
		);

		$scanner = new Log_Scanner();

		try {

			$scan_result = $scanner->scan();
			if ( ! $scan_result[0] ) {
				throw new \Exception( $scan_result[1] );
			}

			$entries = is_array( $scanner->entries ) ? $scanner->entries : array();

			$repo  = new Log_Repository();
			$total = 0;

			foreach ( $entries as $entry ) {
				$repo->insert_or_update( $entry );
				++$total;
			}

			// Prune old db logs once per scan.
			$repo->cleanup_old_logs();

			// Calculate unique issues (dedupe by normalized message).
			$unique_keys = array();

			foreach ( $entries as $entry ) {

				if ( ! isset( $entry['raw'] ) ) {
					continue;
				}

				$normalized = preg_replace( '/^\[[^\]]+\]\s*/', '', $entry['raw'] );
				$key        = md5( $normalized );

				$unique_keys[ $key ] = true;
			}

			$unique_count = count( $unique_keys );

			$result['total']   = $total;
			$result['unique']  = $unique_count;
			$result['entries'] = $entries;
			$result['first']   = $entries[0]['timestamp'] ?? null;
			$result['last']    = ! empty( $entries ) ? end( $entries )['timestamp'] : null;

			// No logs found.
			if ( empty( $entries ) ) {
				$result['success'] = true;
				$result['message'] = __( 'Scan completed. No new logs found.', 'error-monitor' );
				return $result;
			}

			// Success.
			$result['success'] = true;
			$result['message'] = sprintf(
				/* translators: %d: Number of new log entries found in this scan. */
				__( 'Scan completed. %d new log(s) found.', 'error-monitor' ),
				$total
			);

			$this->send_success_email( $entries, $result );

		} catch ( \Throwable $e ) {
			error_log( 'Error_Monitor Scan Failure: ' . $e->getMessage() );

			$error_msg = $scan_result[0] ? $scan_result[1] : $e->getMessage();

			$result['success'] = false;
			$result['message'] = __( 'Scan failed.', 'error-monitor' ) . ' ' . $error_msg;

			$this->send_failure_email( $result['message'] );
		}

		return $result;
	}

	/**
	 * Send success email with logs.
	 */
	private function send_success_email( array $entries, array $result ): void {

		$settings = Settings::get();

		if ( ! Settings::email_configured( $settings ) ) {
			return;
		}

		$mailer = new Mail_SMTP();

		$subject = sprintf(
			'[%s] %d New PHP Error(s) Detected',
			get_bloginfo( 'name' ),
			$result['total']
		);

		$compose = new Compose_Email_Body(
			'log',
			array(
				'count'   => $result['total'],
				'total'   => $result['total'],
				'unique'  => $result['unique'],
				'entries' => $entries,
				'first'   => $result['first'],
				'last'    => $result['last'],
			)
		);

		$html      = $compose->html();
		$plaintext = $compose->plaintext();

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
