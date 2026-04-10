<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * HTTP responder.
 *
 * @package error-monitor
 */


/**
 * Send HTTP JSON response.
 */
class HTTP_Response {


	/**
	 * Send JSON response to client.
	 *
	 * Sets the response header to the passed http status code and a response body of:
	 *  - ok: true or false based on status code.
	 *  - output: array of user-friendly messages.
	 *  - data: any additional data.
	 *
	 * @param array $status [ int(http-code), array(human readable message strings) ].
	 * @param array $data   [ any ].
	 */
	public static function send_json( $status, $data = array() ) {

		if ( ! is_array( $status ) ) {
			error_log( 'Error_Monitor: Function send_json expects array but ' . gettype( $status ) . ' received.' );
			$status = array( 500, 'Service produced an unknown reponse. Your request may have failed.' );
		}

		// Ensure response headers haven't already sent to browser.
		if ( ! headers_sent() ) {
			header( 'Content-Type: application/json; charset=utf-8' );
			status_header( $status[0] );
		}

		// Create response body.
		$response['ok']     = ( $status[0] < 300 ) ? true : false;
		$response['output'] = is_string( $status[1] ) ? array( $status[1] ) : $status[1];
		$response['data']   = $data;

		/**
		 * PHPMailer debug ($mail->SMTPDebug) gets dumped to output buffer and breaks JSON response.
		 * Using ob_clean() before output prevents this.
		 */
		ob_clean();
		echo wp_json_encode( $response );
	}
}
