<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Sanitise Settings.
 *
 * Used as a callback for register_setting().
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */


class Sanitise {

	public function all_settings( $input ) {

		// Merge with existing settings to avoid tabs overwriting non-updated values.
		$existing  = Settings::get();
		$sanitised = array_merge( $existing, $input );

		if ( isset( $input['username'] ) ) {
			$sanitised['username'] = sanitize_text_field( $input['username'] );
		}

		if ( isset( $input['password'] ) ) {
			$sanitised['password'] = $this->sanitise_password( $input['password'] );
		}

		if ( isset( $input['host'] ) ) {
			$sanitised['host'] = $this->validate_domain( $input['host'] );
		}

		if ( isset( $input['port'] ) ) {
			$sanitised['port'] = $this->sanitise_smtp_port( $input['port'] );
		}

		if ( isset( $input['to_email'] ) ) {
			$sanitised['to_email'] = sanitize_email( $input['to_email'] );
		}

		if ( isset( $input['from_email'] ) ) {
			$sanitised['from_email'] = sanitize_email( $input['from_email'] );
		}

		if ( isset( $input['scan_frequency_mins'] ) ) {
			$sanitised['scan_frequency_mins'] = $this->sanitise_minutes( $input['scan_frequency_mins'] );
		}

		if ( isset( $input['last_scan_time'] ) ) {
			$sanitised['last_scan_time'] = $this->sanitize_timestamp( $input['last_scan_time'] );
		}

		if ( isset( $input['last_log_timestamp'] ) ) {
			$sanitised['last_log_timestamp'] = $this->sanitize_timestamp( $input['last_log_timestamp'] );
		}

		return $sanitised;
	}


	/**
	 * Validate a domain name.
	 */
	private function validate_domain( $site_domain ) {
		$ip = gethostbyname( $site_domain );
		$ip = filter_var( $ip, FILTER_VALIDATE_IP );
		if ( $site_domain == '' || $site_domain == null ) {
			return '';
		} elseif ( $ip ) {
			return $site_domain;
		} else {
			return 'INVALID DOMAIN';
		}
	}


	/**
	 * Sanitise an SMTP port number.
	 */
	private function sanitise_smtp_port( $port ) {
		$port_int    = intval( $port );
		$valid_ports = array( 25, 465, 587, 2525 );
		if ( in_array( $port_int, $valid_ports, true ) ) {
			return $port_int;
		} else {
			return '';
		}
	}


	/**
	 * Sanitise a checkbox.
	 */
	private function sanitise_checkbox( $checkbox ) {
		return (bool) $checkbox;
	}


	/**
	 * Sanitise a password.
	 */
	private function sanitise_password( $password ) {
		return trim( $password );
	}


	/**
	 * Sanitise minutes.
	 */
	private function sanitise_minutes( $minutes ) {
		$value = intval( preg_replace( '/[^0-9]/', '', $minutes ) );
		if ( $value > 60 ) {
			$value = 60;
		} elseif ( $value < 1 ) {
			$value = 1;
		}
		return $value;
	}


	/**
	 * Sanitise a timestamp.
	 */
	private function sanitize_timestamp( $timestamp ): int {
		if ( ! is_scalar( $timestamp ) ) {
			return 0;
		}
		$value = trim( (string) $timestamp );
		if ( ! ctype_digit( $value ) ) {
			return 0;
		}
		$timestamp = (int) $value;
		// Validate reasonable Unix timestamp range (1970 → year 2100).
		if ( $timestamp < 0 || $timestamp > 4102444800 ) {
			return 0;
		}
		return $timestamp;
	}
}