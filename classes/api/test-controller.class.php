<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Error Monitor - Test controller.
 *
 * Handle submissions from the admin settings SMTP test button.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */

// WordPress Dependencies.
use WP_REST_Request;

class Test_Controller {

	/**
	 * Receive admin test submissions.
	 */
	public function perform_test( $request ) {

		// Verify request body looks like a test request from the client side script.
		if ( ! is_array( $request ) || ! array_key_exists( 'test', $request ) ) {
			error_log( 'Error_Monitor: perform_test expects request body to be an array containing a "test" key.' );
			return array( 400, 'Test failed: The data recieved does not look like a test request.' );
		}

		$settings = Settings::get();
		if ( false === (bool) $settings ) {
			return array( 500, 'There was a problem retrieving your SMTP settings from the database.' );
		}

		switch ( $request['test'] ) {
			case 'smtp':
				$test_account = new Test_Account();
				$result       = $test_account->smtp_connection(
					$settings['host'],
					$settings['port'],
					$settings['username'],
					$settings['password'],
				);
				return $result;

			case 'email':
				$subject     = sprintf(
					'[%s] SMTP Test Email',
					get_bloginfo( 'name' )
				);
				$from_name   = get_bloginfo( 'name' );
				$reply_name  = $from_name;
				$reply_email = $settings['from_email'];
				$site_domain = wp_parse_url( home_url(), PHP_URL_HOST );
				$compose     = new Compose_Email_Body( 'test' );
				$mailer      = new Mail_SMTP();
				$result      = $mailer->send(
					$settings['host'],
					$settings['port'],
					$settings['username'],
					$settings['password'],
					$settings['to_email'],
					$settings['from_email'],
					$from_name,
					$reply_name,
					$reply_email,
					$subject,
					$compose->html(),
					$compose->plaintext(),
					$site_domain
				);
				return $result;

			default:
				error_log( 'Error_Monitor: perform_test recieved an unknown test type.' );
				return array( 400, 'Test failed:. The requested test does not exist.' );
		}
	}
}
