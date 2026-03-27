<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Error Monitor - Submission controller.
 *
 * Handle form submissions, response messaging and passing of data to the mailer.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */

// WordPress Dependencies.
use WP_REST_Request;
use function get_bloginfo;
use function wp_parse_url;

class Submit_Controller {

	/**
	 * Controller log.
	 */
	private $log = '';

	/**
	 * Receive form submissions.
	 */
	public function error_monitor_rest_api_submit_callback( WP_REST_Request $request ) {

		// Check header is multipart/form-data.
		if ( ! str_contains( $request->get_header( 'Content-Type' ), 'multipart/form-data' ) ) {
			HTTP_Response::send_json( array( 405, 'Sending your message failed due to a malformed request from your browser' ) );

			// Request handlers should exit() when done.
			exit;
		}

		// Get and sort text data between fields and form.
		$body_params = $request->get_body_params();
		$fields      = array();
		$form        = array();
		foreach ( $body_params as $key => $json_data ) {
			$data = json_decode( $json_data, true );
			if ( 'formMeta' === $key ) {
				$form = array(
					'name' => $data['name'],
					'id'   => $data['id'],
				);
			} else {
				if ( empty( $data['value'] ) && $data['required'] === false ) {
					continue;
				}
				$fields[ $key ] = array(
					'value'                => $data['value'],
					'type'                 => $data['type'],
					'id'                   => $data['id'],
					'validationDefinition' => $data['validationDefinition'],
				);
			}
		}

		// Get file data.
		$file_params = $request->get_file_params();
		$files       = array();
		if ( array_key_exists( 'files', $file_params ) ) {
			$number_of_files = count( $file_params['files']['name'] ) - 1;
			for ( $n = 0; $n <= $number_of_files; $n++ ) {
				$files[ $n ] = array(
					'name'     => $file_params['files']['name'][ $n ],
					'tmp_name' => $file_params['files']['tmp_name'][ $n ],
				);
			}
		}

		$form_data = array(
			'form'   => $form,
			'fields' => $fields,
			'files'  => $files,
		);

		/*
		 * Validate data.
		 *
		 * No modified values are returned to front end. Only errors are returned so the user can
		 * update their entries before resubmission. This functionality was chosen to ensure all
		 * submitted data is verified by the user.
		 */
		$Validate            = new Validate();
		$validated_form_data = $Validate->form_data( $form_data );
		if ( $validated_form_data['has_errors'] ) {
			HTTP_Response::send_json( array( 400, __( 'Please correct your input and try again', 'error-monitor' ) ), $validated_form_data );

			// Request handlers should exit() when done.
			exit;
		}

		// Send the email.
		$result = $this->send_email( $form_data );

		// Respond to client.
		HTTP_Response::send_json( $result );

		// Save the form entry to the database.
		CPT_Form_Entry::log_form_entry( $form_data, $result, $this->log );

		// Request handlers should exit() when done.
		exit;
	}


	/**
	 * Send the email.
	 */
	public function send_email( $form_data ) {

		$settings = Settings::get();
		$valid    = Settings::email_configured( $settings );

		$this->log .= date( 'Y-m-d H:i:s' ) . ' Settings ' . ( $valid ? 'OK.' . "\n" : 'Invalid.' . "\n" );

		if ( ! $valid ) {
			$this->log .= date( 'Y-m-d H:i:s' ) . ' ERROR: No mail service configured.' . "\n";
			return array( 503, 'Sending your message failed as no mail service has been configured' );
		}

		$fields  = $form_data['fields'];
		$compose = new Compose_Email_Body( $form_data );

		$from_name      = get_bloginfo( 'name' );
		$reply_name     = isset( $fields['name'] ) ? $fields['name']['value'] : $from_name;
		$reply_email    = isset( $fields['email'] ) ? $fields['email']['value'] : $settings['from_email'];
		$site_domain    = wp_parse_url( html_entity_decode( get_bloginfo( 'url' ) ), PHP_URL_HOST );
		$subject        = 'New ' . strtolower( $form_data['form']['name'] ) . ' form submission from ' . $site_domain;
		$html_body      = $compose->html();
		$plaintext_body = $compose->plaintext();

		if ( $settings ) {
			$this->log .= date( 'Y-m-d H:i:s' ) . ' Attempt SMTP mail.' . "\n";

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

			if ( 200 !== $result[0] ) {
				$this->log .= date( 'Y-m-d H:i:s' ) . ' SMTP mailer reported: "' . $result[1] . "\n";
				error_log( 'Error Monitor: SMTP mailer reported: "' . $result[1] . '"' );
			}
		}

		$this->log .= date( 'Y-m-d H:i:s' ) . ' send_email() result: "' . $result[1] . "\n";
		return $result;
	}
}
