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

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Explicit transaction control is required for atomic update logic.
		$wpdb->query( 'START TRANSACTION' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Repository reads current row for dedupe and counter updates.
		$existing = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Internal table name is trusted and set by plugin code.
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

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Repository writes grouped log updates to plugin-owned table.
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

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Repository inserts grouped log rows to plugin-owned table.
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

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Explicit transaction control is required for atomic update logic.
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

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Repository reads recent logs from plugin-owned table for admin/API rendering.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Internal table name is trusted and set by plugin code.
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
		usort(
			$entries,
			function ( $a, $b ) {
				return $a['timestamp'] <=> $b['timestamp'];
			}
		);

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

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Repository performs periodic retention cleanup on plugin-owned table.
		$wpdb->query(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Internal table name is trusted and set by plugin code.
				"DELETE FROM {$this->table} WHERE last_seen < %d",
				$cutoff
			)
		);
	}
}
