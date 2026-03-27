<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Error Monitor - PHPMailer SMTP Account Test Handler.
 *
 * This uses the SMTP class alone to check that a connection can be made to an SMTP server,
 * authenticate, then disconnect
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require ERRORMONITOR_PATH . 'vendor/autoload.php';


class Test_Account {

	/**
	 * Pretty log messages for test output.
	 *
	 * @var array Strings of log messages.
	 */
	private $pretty_log = array();

	/**
	 * Raw mailer log messages for test output.
	 *
	 * @var array Strings of log messages.
	 */
	private $debug_log = array();

	/**
	 * Test SMTP connection.
	 *
	 * @return array containing HTTP status code and a status message.
	 */
	public function smtp_connection( $host, $port, $username, $password ) {
		$smtp       = new SMTP();
		$data       = array();
		$timeout    = 10;

		$this->debug_log[] = "\n";
		$this->debug_log[] = '########## Raw debug log ##########';

		// DEBUG_OFF for production or DEBUG_SERVER while debugging.
		$smtp->do_debug    = SMTP::DEBUG_SERVER;
		$smtp->Debugoutput = function ( $message, $level ) {
			$levels      = array(
				1 => '[COMMAND] ',
				2 => '[RESPONSE] ',
				3 => '[CONNECT] ',
				4 => '[SOCKET] ',
			);
			$this->debug_log[] = $levels[ (int) $level ] . $message;
		};

		try {
			$this->pretty_log[] = 'Testing: ' . $host . ':' . $port;

			// Decide how to handle TLS based on the port.
			$use_starttls = false; // STARTTLS (upgrade after EHLO).
			$implicit_tls = false; // TLS from the first byte (port 465).
			$connect_host = $host; // May be wrapped with ssl:// for implicit TLS.

			switch ( (int) $port ) {
				case 465:
					// Implicit TLS (SMTPS) – connect via ssl:// and DO NOT call startTLS().
					$this->pretty_log[] = 'Choosing implicit TLS for port ' . $port;
					$implicit_tls = true;
					if ( stripos( $host, 'ssl://' ) !== 0 && stripos( $host, 'tls://' ) !== 0 ) {
						$connect_host = 'ssl://' . $host;
					}
					break;

				case 587:
				case 2525:
					// Submission ports – usually plain connect + STARTTLS.
					$this->pretty_log[] = 'Choosing opportunistic STARTTLS for port ' . $port;
					$use_starttls = true;
					break;

				case 25:
				default:
					// Port 25 often supports opportunistic STARTTLS.
					// You can set $use_starttls = false here if you want to skip it.
					$this->pretty_log[] = 'Choosing opportunistic STARTTLS for port ' . $port;
					$use_starttls = true;
					break;
			}

			// Connect to server with appropriate transport.
			if ( ! $smtp->connect( $connect_host, $port, $timeout ) ) {
				$this->pretty_log[] = 'Connect - FAIL';
				throw new Exception( 'Connect failed: ' . ( $smtp->getError()['error'] ?? 'Unknown error' ) );
			} else {
				$this->pretty_log[] = 'Connect - PASS';
			}

			// EHLO/HELO.
			if ( ! $smtp->hello( gethostname() ) ) {
				$this->pretty_log[] = 'EHLO - FAIL';
				throw new Exception( 'EHLO failed: ' . ( $smtp->getError()['error'] ?? 'Unknown error' ) );
			} else {
				$this->pretty_log[] = 'EHLO - PASS';
			}

			$services     = $smtp->getServerExtList();
			$this->pretty_log[] = 'Server supports: ' . implode( ', ', array_keys( $services ) );

			/**
			 * Try STARTTLS.
			 * Only try STARTTLS if:
			 *  - we decided to use it for this port, and
			 *  - the server advertises STARTTLS, and
			 *  - we are NOT already on implicit TLS (port 465).
			 */
			if ( $use_starttls && ! $implicit_tls && is_array( $services ) && array_key_exists( 'STARTTLS', $services ) ) {
				if ( ! $smtp->startTLS() ) {
					$this->pretty_log[] = 'Start encryption (STARTTLS) - FAIL';
					throw new Exception( 'Failed to start encryption: ' . ( $smtp->getError()['error'] ?? 'Unknown error' ) );
				} else {
					$this->pretty_log[] = 'Start encryption (STARTTLS) - PASS';
				}
				// Repeat EHLO after STARTTLS
				if ( ! $smtp->hello( gethostname() ) ) {
					$this->pretty_log[] = 'EHLO with STARTTLS - FAIL';
					throw new Exception( 'EHLO with STARTTLS failed: ' . ( $smtp->getError()['error'] ?? 'Unknown error' ) );
				} else {
					$this->pretty_log[] = 'EHLO with STARTTLS - PASS';
				}
				$services = $smtp->getServerExtList();
			} else {
				$this->pretty_log[] = 'STARTTLS not offered by ' . $connect_host;
			}

			// Authenticate.
			if ( ! $smtp->authenticate( $username, $password ) ) {
				$this->pretty_log[] = 'Authentication - FAIL.';
				throw new Exception( 'Authentication failed: ' . ( $smtp->getError()['error'] ?? 'Unknown error' ) );
			} else {
				$this->pretty_log[] = 'Authentication - PASS.';
			}

			$this->pretty_log[] = '🟢 All tests passed! SMTP configuration is valid.';
			return array( 200, $this->pretty_log );

		} catch ( Exception $e ) {
			$this->pretty_log[] = '🔴 SMTP test failed: ' . $e->getMessage();
			return array( 500, array_merge( $this->pretty_log, $this->debug_log ) );
		}

		// Whatever happened, close the connection.
		$smtp->quit();
	}
}
