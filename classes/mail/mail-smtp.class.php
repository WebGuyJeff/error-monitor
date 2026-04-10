<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * PHPMailer SMTP Mail Handler.
 *
 * Send an email via PHPMailer using the SMTP account configured by the user.
 *
 * @package error-monitor
 */

// Import PHPMailer classes into the global namespace.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader.
require ERRORMONITOR_PATH . 'vendor/autoload.php';

class Mail_SMTP {

	/**
	 * Send an email.
	 */
	public function send(
		$host,
		$port,
		$username,
		$password,
		$to_email,
		$from_email,
		$from_name,
		$reply_name,
		$reply_email,
		$subject,
		$html_body,
		$plaintext_body,
		$site_domain,
	) {

		// Make sure PHP server script limit is higher than mailer timeout.
		set_time_limit( 60 );
		$mail = new PHPMailer( true );

		try {
			// SMTP::DEBUG_OFF in production or DEBUG_SERVER while debugging.
			$mail->SMTPDebug   = SMTP::DEBUG_OFF;
			$mail->Debugoutput = 'error_log';

			// Set PHPMailer to use SMTP.
			$mail->isSMTP();
			$mail->Host     = $host;
			$mail->Port     = (int) $port;
			$mail->SMTPAuth = true;
			$mail->Username = $username;
			$mail->Password = $password;
			$mail->CharSet  = 'UTF-8';
			$mail->Helo     = $site_domain;

			// Connection timeout (secs).
			$mail->Timeout = 30;

			// Time allowed for each SMTP command response.
			$mail->getSMTPInstance()->Timelimit = 10;

			// We'll decide the use of TLS manually after probing the server for capabilities.
			$mail->SMTPAutoTLS = false;
			$mail->SMTPSecure  = '';

			// ---------- Phase 1: probe server extensions ---------- //

			$smtp    = new SMTP();
			$timeout = 10;

			// For implicit SSL (port 465), we don't need to probe STARTTLS, but it doesn't hurt.
			$probe_host       = $host;
			$use_implicit_tls = ( 465 === $port );

			if ( $use_implicit_tls ) {
				// For the probe, connect with implicit TLS (like ssl://host:465).
				if ( stripos( $host, 'ssl://' ) !== 0 && stripos( $host, 'tls://' ) !== 0 ) {
					$probe_host = 'ssl://' . $host;
				}
			}

			if ( ! $smtp->connect( $probe_host, $port, $timeout ) ) {
				throw new Exception( 'Connect failed: ' . ( $smtp->getError()['error'] ?? 'Unknown error' ) );
			}

			if ( ! $smtp->hello( gethostname() ?: 'localhost' ) ) {
				$smtp->quit( true );
				throw new Exception( 'Capability probe EHLO failed: ' . $smtp->getError()['error'] );
			}

			$caps = $smtp->getServerExtList() ?: array();
			// Close the probe connection; PHPMailer will reconnect with our chosen settings.
			$smtp->quit( true );

			// ---------- Phase 2: decide TLS settings based on port + extensions ---------- //

			if ( $use_implicit_tls ) {
				// Port 465 → implicit TLS and no STARTTLS on top of SMTPS.
				$mail->SMTPSecure  = PHPMailer::ENCRYPTION_SMTPS;
				$mail->SMTPAutoTLS = false;
			} else {
				// Other ports ( 25/587/2525... ), use STARTTLS only if the server advertises it.
				if ( isset( $caps['STARTTLS'] ) ) {
					// Server offers STARTTLS → explicitly enable it. No auto control required.
					$mail->SMTPSecure  = PHPMailer::ENCRYPTION_STARTTLS;
					$mail->SMTPAutoTLS = false;
				} else {
					// No STARTTLS offered → allow opportunistic TLS just in case.
					$mail->SMTPSecure  = '';
					$mail->SMTPAutoTLS = true;
				}
			}

			// ---------- Content ---------- //

			$mail->setFrom( $from_email, $from_name );
			$mail->addAddress( $to_email, );
			if ( isset( $reply_name ) && isset( $reply_email ) ) {
				$mail->addReplyTo( $reply_email, $reply_name );
			}
			$mail->isHTML( true );
			$mail->Subject = $subject;
			$mail->Body    = $html_body;
			$mail->AltBody = $plaintext_body;

			// ---------- Send ---------- //

			$sent = $mail->send();
			if ( $sent ) {
				return array( 200, 'Message sent successfully.' );
			} else {
				throw new Exception( $mail->ErrorInfo );
			}
		} catch ( Exception $e ) {

			// PHPMailer exceptions are not public-safe - Send to logs.
			error_log( 'Error_Monitor: ' . $e );
			// Generic public error.
			return array( 500, 'Sending your message failed while connecting to the mail server. Please try again.' );
		}
	}
}
