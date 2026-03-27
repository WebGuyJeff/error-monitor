<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Error Monitor - Inline Script.
 *
 * Generates inline script ready for inlining with client-side JavaScript.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */
class Inline_Script {

	private const JS_OBJECT_NAME = 'errorMonitorInlinedScript';


	/**
	 * Generate JavaScript to be inlined by wp_add_inline_script().
	 *
	 * This is how we pass backend variables to cient-side JS.
	 */
	public static function get_variables() {
		return self::JS_OBJECT_NAME . ' = ' . wp_json_encode(
			array(
				'settingsOK'  => Settings::email_configured(),
				'restTestURL' => get_rest_url( null, 'webguyjeff/error-monitor/v1/test' ),
				'restNonce'   => wp_create_nonce( 'wp_rest' ),
			)
		);
	}
}
