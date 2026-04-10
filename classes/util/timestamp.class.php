<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Get and format timestamps.
 *
 * @package error-monitor
 * @copyright Copyright 2023 Web Guy Jeff
 */


class Timestamp {

	/**
	 * Get unix timestamp.
	 */
	public static function get_unix() {
		return time();
	}

	public static function unix_to_readable( $timestamp ) {
		return gmdate( 'H:i:s d/m/Y', $timestamp );
	}
}
