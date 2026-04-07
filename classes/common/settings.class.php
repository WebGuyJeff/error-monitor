<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Error Monitor - Get Settings and Validate From DB.
 *
 * This class fetches the settings from the database and validates their
 * values before passing them back to caller. If ANY of the settings are
 * invalid, returns false.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */

// Import PHPMailer for use of the email validation method.
use PHPMailer\PHPMailer\PHPMailer;

// Load Composer's autoloader (includes vendor/PHPMailer)
require ERRORMONITOR_PATH . 'vendor/autoload.php';

class Settings {


	/**
	 * Get plugin settings.
	 *
	 * Gets plugin settings from the database.
	 */
	public static function get( $keys = null ) {

		$settings = get_option( 'error_monitor_settings' );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		// Return ALL settings.
		if ( null === $keys ) {
			return is_array( $settings ) ? $settings : array();
		}

		// Single key.
		if ( is_string( $keys ) ) {
			return isset( $settings[ $keys ] ) ? $settings[ $keys ] : null;
		}

		// Multiple keys.
		if ( is_array( $keys ) ) {
			$result = array();

			foreach ( $keys as $key ) {
				if ( isset( $settings[ $key ] ) ) {
					$result[ $key ] = $settings[ $key ];
				}
			}

			return $result;
		}

		return null;
	}


	/**
	 * Get plugin settings or default.
	 *
	 * Gets plugin settings from the database or returns default value.
	 */
	public static function get_or_default( $key, $default = null ) {
		$value = self::get( $key );

		return ( null === $value ) ? $default : $value;
	}


	/**
	 * Set plugin settings.
	 *
	 * Sets plugin settings in the database.
	 */
	public static function set( $key, $value ) {

		if ( ! is_string( $key ) || empty( $key ) || ! isset( $value ) ) {
			return false;
		}

		$current = get_option( 'error_monitor_settings', array() );

		if ( ! is_array( $current ) ) {
			$current = array();
		}

		$updated = false;
		if ( self::validate( array( $key => $value ) ) ) {
			$current[ $key ] = $value;
			$updated         = true;
		}

		$yesNO = $updated ? 'Yes' : 'No';
		$validYesNo = self::validate( array( $key => $value ) ) ? 'Yes' : 'No';
		error_log( 'Settings::set | updated: ' . $yesNO . ' / valid: ' . $validYesNo . ' | ' . $key . ' = ' . print_r( $value, true ) );

		if ( ! $updated ) {
			return false;
		}

		update_option( 'error_monitor_settings', $current );

		return true;
	}


	/**
	 * Check settings are ready to send email.
	 */
	public static function email_configured( $settings = array() ) {

		if ( empty( $settings ) ) {
			$settings = self::get();
		}

		if ( ! $settings || empty( $settings ) ) {
			return false;
		}

		if ( empty( $settings['from_email'] )
			|| empty( $settings['to_email'] )
			|| empty( $settings['username'] )
			|| empty( $settings['password'] )
			|| empty( $settings['host'] )
			|| empty( $settings['port'] ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Validate settings
	 *
	 * Returns false if ANY setting is invalid.
	 * This only validates settings and should not manipulate values.
	 */
	public static function validate( $settings ) {

		foreach ( $settings as $name => $value ) {

			$valid = true;
			switch ( $name ) {
				case 'username':
					$valid = ( is_string( $value ) && mb_strlen( $value ) < 254 ) ? true : false;
					break;

				case 'password':
					$valid = ( is_string( $value ) && mb_strlen( $value ) < 100 ) ? true : false;
					break;

				case 'host':
					if ( ! is_string( $value ) ) {
						$valid = false;
						break;
					}
					$value = trim( $value );
					if ( filter_var( $value, FILTER_VALIDATE_IP ) ) {
						$valid = true;
						break;
					}
					// Validate hostname (RFC-compliant-ish).
					$valid = (bool) preg_match(
						'/^(?=.{1,253}$)(?!-)([a-zA-Z0-9-]{1,63}\.)*[a-zA-Z0-9-]{1,63}$/',
						$value
					);
					break;

				case 'port':
					$valid_ports = array( 25, 465, 587, 2525 );
					$valid       = in_array( intval( $value ), $valid_ports, true );
					break;

				case 'from_email':
				case 'to_email':
					$valid = ( PHPMailer::validateAddress( $value ) ) ? true : false;
					break;

				case 'scan_frequency_mins':
				case 'log_retention_days':
				case 'last_scan_time':
				case 'last_log_timestamp':
					$valid = ( is_numeric( $value ) && intval( $value ) > 0 ) ? true : false;
					break;

				case 'log_file_path':
					$valid = is_string( $value );
					break;

				case 'monitor_enabled':
					$valid = in_array( $value, array( 0, 1, '0', '1' ), true );
					break;

				default:
					// Invalid setting name - fail the validation.
					$valid = false;

			}
			if ( $valid === false ) {
				// fail - we're done here.
				return false;
			}
		}

		// pass.
		return true;
	}
}
