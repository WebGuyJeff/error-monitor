<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Log Formatter.
 *
 * Handles transformation of log entries for presentation.
 *
 * @package error-monitor
 */
class Log_Formatter {

	/**
	 * Group logs by timestamp and deduplicate within each timestamp.
	 *
	 * @param array $entries Raw log entries.
	 * @return array
	 */
	public static function grouped( array $entries ): array {

		$grouped = array();

		foreach ( $entries as $entry ) {

			$timestamp = $entry['timestamp'] ?? null;

			if ( ! $timestamp ) {
				continue;
			}

			$normalized = preg_replace( '/^\[[^\]]+\]\s*/', '', $entry['raw'] );

			$key = md5( $normalized );

			if ( ! isset( $grouped[ $timestamp ] ) ) {
				$grouped[ $timestamp ] = array();
			}

			if ( isset( $grouped[ $timestamp ][ $key ] ) ) {

				++$grouped[ $timestamp ][ $key ]['count'];

			} else {

				$grouped[ $timestamp ][ $key ] = array(
					'message'  => trim( $normalized ),
					'severity' => $entry['severity'],
					'count'    => 1,
				);
			}
		}

		ksort( $grouped );

		return $grouped;
	}

	/**
	 * Return logs in flat chronological order.
	 *
	 * @param array $entries Raw log entries.
	 * @return array
	 */
	public static function flat( array $entries ): array {

		usort(
			$entries,
			function ( $a, $b ) {
				return $a['timestamp'] <=> $b['timestamp'];
			}
		);

		return $entries;
	}
}
