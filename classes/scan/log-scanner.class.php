<?php

namespace WebGuyJeff\Error_Monitor;

class Log_Scanner {

	public array $entries = array();


	/**
	 * Run scan and populate properties.
	 */
	public function scan(): array {

		$log_file = Settings::get( 'log_file_path' );
		if ( ! $log_file || ! file_exists( $log_file ) || ! is_readable( $log_file ) ) {
			// No log file configured in settings.

			$discovery = new Log_File_Discovery();
			$log_file  = $discovery->get_log_file_path();

			if ( ! $log_file || ! file_exists( $log_file ) || ! is_readable( $log_file ) ) {
				// No log file auto-discovered.

				return array( false, __( 'No log file configured or not discoverable.', 'error-monitor' ) );
			}
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

		return array( true, __( 'Scan complete', 'error-monitor' ) );
	}


	/**
	 * Read file safely from end using SplFileObject.
	 */
	private function read_from_end( string $file, int $last_timestamp ): int {

		$new_timestamp = $last_timestamp;
		$max_logs      = 100;

		$file_obj = new \SplFileObject( $file, 'r' );

		$file_obj->seek( PHP_INT_MAX );
		$last_line = $file_obj->key();

		$entries       = array();
		$current_entry = array();

		for ( $i = $last_line; $i >= 0; $i-- ) {

			$file_obj->seek( $i );
			$line = rtrim( (string) $file_obj->current(), "\r\n" );

			$timestamp = $this->extract_timestamp( $line );

			if ( null !== $timestamp ) {

				// Save previous entry.
				if ( ! empty( $current_entry ) ) {

					$entries[]     = implode( "\n", array_reverse( $current_entry ) );
					$current_entry = array();

					if ( count( $entries ) >= $max_logs ) {
						break;
					}
				}

				// Stop if already processed.
				if ( $timestamp <= $last_timestamp ) {
					break;
				}

				// Start new entry
				$current_entry[] = $line;

				if ( $timestamp > $new_timestamp ) {
					$new_timestamp = $timestamp;
				}
			} else {

				// Continuation line (multiline logs).
				if ( ! empty( $current_entry ) ) {
					$current_entry[] = $line;
				}
			}
		}

		// Catch last entry.
		if ( ! empty( $current_entry ) && count( $entries ) < $max_logs ) {
			$entries[] = implode( "\n", array_reverse( $current_entry ) );
		}

		// Restore chronological order.
		$entries = array_reverse( $entries );

		$this->entries = $this->build_entries( $entries );

		return $new_timestamp;
	}


	/**
	 * Extract timestamp from log line.
	 */
	private function extract_timestamp( string $line ): ?int {

		if ( preg_match( '/^\[(.*?)\]/', $line, $matches ) ) {
			$time = strtotime( $matches[1] );
			return false !== $time ? $time : null;
		}

		return null;
	}


	/**
	 * Convert raw log entries into structured entries.
	 */
	private function build_entries( array $entries ): array {

		$results = array();

		foreach ( $entries as $entry ) {

			$lines      = explode( "\n", $entry );
			$first_line = $lines[0] ?? '';

			$timestamp = $this->extract_timestamp( $first_line );

			if ( null === $timestamp ) {
				continue;
			}

			$normalized = preg_replace( '/^\[.*?\]\s*/', '', $entry );

			$severity = 'notice';

			// Normalize once.
			$line = strtolower( $entry );

			// Detect real PHP error types only.
			if ( preg_match( '/php\s+(fatal error|parse error)/i', $entry ) ) {
				$severity = 'error';

			} elseif ( preg_match( '/php\s+warning/i', $entry ) ) {
				$severity = 'warning';

			} elseif ( preg_match( '/php\s+(notice|deprecated)/i', $entry ) ) {
				$severity = 'notice';
			}

			// Fallback for non-PHP logs with strict exclusions for plugin name.
			if (
				preg_match( '/\b(fatal|error)\b/i', $entry ) &&
				! preg_match( '/\berror[\s\-]?monitor\b/i', $entry )
			) {
				$severity = 'error';

			} elseif ( preg_match( '/\bwarning\b/i', $entry ) ) {
				$severity = 'warning';
			}

			$results[] = array(
				'raw'        => $entry,
				'normalized' => trim( (string) $normalized ),
				'severity'   => $severity,
				'timestamp'  => $timestamp,
			);
		}

		return $results;
	}
}
