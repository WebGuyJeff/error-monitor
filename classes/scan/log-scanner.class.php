<?php

namespace WebGuyJeff\Error_Monitor;

class Log_Scanner {

	public int $count   = 0;
	public string $logs = '';

	/**
	 * Run scan and populate properties.
	 *
	 * @return void
	 */
	public function scan(): void {
		$log_file = $this->get_log_file_path();

		if ( ! $log_file || ! file_exists( $log_file ) || ! is_readable( $log_file ) ) {
			return;
		}

		$last_timestamp = Settings::get( 'last_log_timestamp' );

		if ( ! $last_timestamp ) {
			$last_timestamp = 0;
		}

		// Store scan time immediately.
		Settings::set( 'last_scan_time', (int) time() );

		$new_timestamp = $this->read_from_end( $log_file, (int) $last_timestamp );

		// Update last processed log timestamp.
		if ( $new_timestamp > $last_timestamp ) {
			Settings::set( 'last_log_timestamp', (int) $new_timestamp );
		}
	}

	/**
	 * Read file safely from end using SplFileObject.
	 *
	 * @param string $file
	 * @param int    $last_timestamp
	 * @return int
	 */
	private function read_from_end( string $file, int $last_timestamp ): int {
		$new_timestamp = $last_timestamp;
		$max_logs      = 100;

		$file_obj = new \SplFileObject( $file, 'r' );

		// Jump to last line.
		$file_obj->seek( PHP_INT_MAX );
		$last_line = $file_obj->key();

		$collected = array();

		for ( $i = $last_line; $i >= 0; $i-- ) {
			$file_obj->seek( $i );

			$line = trim( (string) $file_obj->current() );

			if ( '' === $line ) {
				continue;
			}

			$current_timestamp = $this->extract_timestamp( $line );

			if ( null === $current_timestamp ) {
				continue;
			}

			// Stop when reaching already processed logs.
			if ( $current_timestamp <= $last_timestamp ) {
				break;
			}

			$collected[] = $line;

			if ( $current_timestamp > $new_timestamp ) {
				$new_timestamp = $current_timestamp;
			}

			// Enforce limit.
			if ( count( $collected ) >= $max_logs ) {
				break;
			}
		}

		// Restore chronological order.
		$collected = array_reverse( $collected );

		$this->count = count( $collected );
		$this->logs  = $this->count ? implode( "\n", $collected ) . "\n" : '';

		return $new_timestamp;
	}

	/**
	 * Extract timestamp from log line.
	 *
	 * @param string $line
	 * @return int|null
	 */
	private function extract_timestamp( string $line ): ?int {
		if ( preg_match( '/^\[(.*?)\]/', $line, $matches ) ) {
			$time = strtotime( $matches[1] );

			return false !== $time ? $time : null;
		}

		return null;
	}

	/**
	 * Get log file path.
	 *
	 * @return string|null
	 */
	private function get_log_file_path(): ?string {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			if ( is_string( WP_DEBUG_LOG ) ) {
				return WP_DEBUG_LOG;
			}

			return WP_CONTENT_DIR . '/debug.log';
		}

		$fallback = ini_get( 'error_log' );

		if ( $fallback && file_exists( $fallback ) ) {
			return $fallback;
		}

		return null;
	}
}
