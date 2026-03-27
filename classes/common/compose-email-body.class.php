<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Error Monitor - Compose email body.
 *
 * Compose HTML or plain text email body.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */

// WordPress Dependencies.
use function get_bloginfo;
use function wp_strip_all_tags;
use function plugin_dir_path;
use function get_site_url;
use function wp_parse_url;


class Compose_Email_Body {

	/**
	 * The submitted form name.
	 */
	private $notification_name = '';

	/**
	 * The website URL.
	 */
	private $site_domain = '';

	/**
	 * The submitted form field data.
	 */
	private $fields = array();

	/**
	 * Email header.
	 */
	private $email_header = '';


	/**
	 * Initialise class properties.
	 *
	 * @param array $form_data Submitted form data.
	 */
	public function __construct( $form_data ) {
		$this->notification_name = ucfirst( $form_data['form']['name'] );
		$this->site_domain       = wp_parse_url( html_entity_decode( get_bloginfo( 'url' ) ), PHP_URL_HOST );
		$this->fields            = $form_data['fields'];
		$this->email_header      = "<b>{$this->notification_name}</b> recieved from {$this->site_domain}";
	}


	/**
	 * Compose HTML body.
	 */
	public function html() {
		$html = Util::include_with_vars(
			ERRORMONITOR_PATH . 'parts/email.php',
			array(
				$this->notification_name,
				$this->site_domain,
				$this->fields,
				$this->email_header,
			),
		);
		return $html;
	}


	/**
	 * Compose plain text body.
	 */
	public function plaintext() {
		$plaintext_header = esc_html( $this->email_header );
		$plaintext_fields = "\n\n";
		foreach ( $this->fields as $name => $data ) {
			$plaintext_fields .= ucfirst( str_replace( '-', ' ', $name ) ) . ': ' . $data['value'] . "\n";
		}
		$plaintext_fields .= "\n\n";
		$plaintext         = <<<PLAIN
			{$plaintext_header}
			{$plaintext_fields}
			You are viewing the plaintext version of this email because you have disallowed HTML
			content in your email client. To view this and any future messages from this sender in
			complete HTML formatting, try adding the sender domain to your spam filter whitelist.
		PLAIN;
		$plaintext_cleaned = wp_strip_all_tags( $plaintext );
		return $plaintext_cleaned;
	}
}
