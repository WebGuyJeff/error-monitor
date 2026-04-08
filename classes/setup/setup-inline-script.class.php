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
class Setup_Inline_Script {

	private const JS_OBJECT_NAME = 'errorMonitorInlinedScript';

	public static function get_variables() {
		return self::JS_OBJECT_NAME . ' = ' . wp_json_encode(
			array(
				'restBaseURL' => untrailingslashit( get_rest_url( null, 'webguyjeff/error-monitor/v1' ) ),
				'restNonce'   => wp_create_nonce( 'wp_rest' ),
				'pluginSlug'  => Admin_Page::url( 'base', 'slug' ),
			)
		);
	}
}
