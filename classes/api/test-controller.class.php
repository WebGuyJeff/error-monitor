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
	public function error_monitor_test_smtp_rest_api_callback( WP_REST_Request $request ) {

		// Verify header Content-Type of the request is what we want to see.
		$type = $request->get_header( 'Content-Type' );
		if ( ! str_contains( $type, 'application/json' ) ) {
			HTTP_Response::send_json( array( 406, 'Test failed: Header Content-Type has an unexpected value.' ) );
			error_log( 'Error_Monitor: error_monitor_test_smtp_rest_api_callback expects application/json but header Content-Type ' . $type . ' received.' );
			exit;
		}

		// Verify request body looks like a test request from the client side script.
		$test_request = json_decode( $request->get_body(), true );
		if ( ! is_array( $test_request ) || ! array_key_exists( 'test', $test_request ) ) {
			HTTP_Response::send_json( array( 400, 'Test failed: The data recieved does not look like a test request.' ) );
			error_log( 'Error_Monitor: error_monitor_test_smtp_rest_api_callback expects request body to be an array containing a "test" key.' );
			exit;
		}

		$settings = Settings::get();
		if ( false === (bool) $settings ) {
			return array( 500, 'There was a problem retrieving your SMTP settings from the database.' );
		}

		switch ( $test_request['test'] ) {
			case 'smtp':
				$test_account = new Test_Account();
				$result       = $test_account->smtp_connection(
					$settings['host'],
					$settings['port'],
					$settings['username'],
					$settings['password'],
				);
				HTTP_Response::send_json( $result );
				exit;

			case 'email':
				$test_data = array(
					'form' => array(
						'id'   => 0,
						'name' => 'email test',
					),
					'fields' => array(
						'name'  => array(
							'value' => 'Test Name',
						),
						'email' => array(
							'value' => 'test@example.com',
						),
					),
				);

				$compose        = new Compose_Email_Body( $test_data );
				$from_name      = get_bloginfo( 'name' );
				$reply_name     = isset( $fields['name'] ) ? $fields['name']['value'] : $from_name;
				$reply_email    = isset( $fields['email'] ) ? $fields['email']['value'] : $settings['from_email'];
				$site_domain    = wp_parse_url( html_entity_decode( get_bloginfo( 'url' ) ), PHP_URL_HOST );
				$subject        = '🥳 Success! ' . ucfirst( $test_data['form']['name'] ) . ' recieved from ' . $site_domain;
				$html_body      = $compose->html();
				$plaintext_body = $compose->plaintext();

				$mailer         = new Mail_SMTP();
				$result         = $mailer->send(
					$host       = $settings['host'],
					$port       = $settings['port'],
					$username   = $settings['username'],
					$password   = $settings['password'],
					$to_email   = $settings['to_email'],
					$from_email = $settings['from_email'],
					$from_name,
					$reply_name,
					$reply_email,
					$subject,
					$html_body,
					$plaintext_body,
					$site_domain,
				);

				HTTP_Response::send_json( $result );
				exit;

			default:
				HTTP_Response::send_json( array( 400, 'Test failed:. The requested test does not exist.' ) );
				error_log( 'Error_Monitor: error_monitor_test_smtp_rest_api_callback recieved an unknown test type.' );
				exit;
		}
	}
}
