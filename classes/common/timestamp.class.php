<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Get and format timestamps.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright 2023 Jefferson Real
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
