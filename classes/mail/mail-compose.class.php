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
use function wp_parse_url;
use function esc_html;

class Mail_Compose {

	const TEMPLATE_PATH = ERRORMONITOR_PATH . 'classes/mail/templates/';

	private string $type;
	private array $data;
	private string $site_domain;
	private string $email_header;

	public function __construct( string $type, array $data = array() ) {

		$this->type        = $type;
		$this->data        = $data;
		$this->site_domain = wp_parse_url( html_entity_decode( get_bloginfo( 'url' ) ), PHP_URL_HOST );

		switch ( $type ) {

			case 'test':
				$this->email_header = 'SMTP Test Successful - ' . $this->site_domain;
				break;

			case 'log':
				$count              = $data['count'] ?? 0;
				$this->email_header = sprintf(
					'%d New PHP Error(s) Detected - %s',
					$count,
					$this->site_domain
				);
				break;

			default:
				$this->email_header = 'Notification - ' . $this->site_domain;
		}
	}

	public function html(): string {

		$content = '';

		switch ( $this->type ) {

			case 'test':
				$content = Template::include_with_vars(
					self::TEMPLATE_PATH . 'partial-test.php',
					array( $this->data )
				);
				break;

			case 'log':
				$content = Template::include_with_vars(
					self::TEMPLATE_PATH . 'partial-log.php',
					array( $this->data )
				);
				break;
		}

		$layout = ( $this->type === 'log' ) ? 'full-width' : 'centered';

		return Template::include_with_vars(
			self::TEMPLATE_PATH . 'email.php',
			array(
				$this->email_header,
				$content,
				$layout,
			)
		);
	}

	public function plaintext(): string {

		switch ( $this->type ) {

			case 'test':
				return "SMTP Test Successful\nYour email configuration is working correctly.";

			case 'log':
				return sprintf(
					"%d new error(s) detected\n\n%s",
					$this->data['count'] ?? 0,
					$this->data['logs'] ?? ''
				);
		}

		return '';
	}
}
