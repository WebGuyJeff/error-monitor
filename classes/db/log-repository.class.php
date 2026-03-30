<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Log Repository.
 *
 * Handles database storage and retrieval of logs.
 *
 * @package error-monitor
 */
class Log_Repository {

	private string $table;

	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'error_monitor_logs';
	}


	public function insert_or_update( array $entry ): void {

		global $wpdb;

		$hash         = md5( $entry['normalized'] );
		$current_time = (int) $entry['timestamp'];

		$wpdb->query( 'START TRANSACTION' );

		$existing = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE hash = %s LIMIT 1 FOR UPDATE",
				$hash
			),
			ARRAY_A
		);

		if ( $existing ) {

			$timestamps = json_decode( $existing['timestamps'], true );

			if ( ! is_array( $timestamps ) ) {
				$timestamps = array();
			}

			$timestamps[] = $current_time;

			if ( count( $timestamps ) > 100 ) {
				$timestamps = array_slice( $timestamps, -100 );
			}

			$wpdb->update(
				$this->table,
				array(
					'timestamps' => wp_json_encode( $timestamps ),
					'count'      => (int) $existing['count'] + 1,
					'last_seen'  => $current_time,
				),
				array( 'id' => $existing['id'] ),
				array( '%s', '%d', '%d' ),
				array( '%d' )
			);

		} else {

			$wpdb->insert(
				$this->table,
				array(
					'hash'               => $hash,
					'message'            => $entry['raw'],
					'normalized_message' => $entry['normalized'],
					'severity'           => $entry['severity'],
					'timestamps'         => wp_json_encode( array( $current_time ) ),
					'count'              => 1,
					'first_seen'         => $current_time,
					'last_seen'          => $current_time,
				),
				array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d' )
			);
		}

		$wpdb->query( 'COMMIT' );
	}


	/**
	 * Get recent logs from database.
	 *
	 * @param int $limit Number of logs.
	 * @return array
	 */
	public function get_recent_logs( int $limit = 200 ): array {

		global $wpdb;

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->table} ORDER BY last_seen DESC LIMIT %d",
				$limit
			),
			ARRAY_A
		);

		if ( ! $rows ) {
			return array();
		}

		$entries = array();

		foreach ( $rows as $row ) {

			$timestamps = json_decode( $row['timestamps'], true );

			if ( ! is_array( $timestamps ) ) {
				$timestamps = array();
			}

			// Expand each timestamp into entry.
			foreach ( $timestamps as $ts ) {
				$entries[] = array(
					'raw'        => $row['message'],
					'normalized' => $row['normalized_message'],
					'timestamp'  => (int) $ts,
					'severity'   => $row['severity'],
				);
			}
		}

		// Sort chronologically.
		usort( $entries, function ( $a, $b ) {
			return $a['timestamp'] <=> $b['timestamp'];
		});

		return $entries;
	}


	/**
	 * Delete logs older than retention period.
	 */
	public function cleanup_old_logs(): void {

		global $wpdb;

		// Prevent running too frequently.
		$last_run = get_transient( 'error_monitor_cleanup_last_run' );

		if ( $last_run ) {
			return;
		}

		set_transient( 'error_monitor_cleanup_last_run', time(), HOUR_IN_SECONDS );

		$days = (int) Settings::get_or_default( 'log_retention_days', 30 );

		if ( $days <= 0 ) {
			return;
		}

		$cutoff = time() - ( $days * DAY_IN_SECONDS );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$this->table} WHERE last_seen < %d",
				$cutoff
			)
		);
	}
}
