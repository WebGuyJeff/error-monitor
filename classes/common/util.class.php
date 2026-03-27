<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * A library of helper functions for WordPress.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright 2023 Jefferson Real
 */


/**
 * Utility methods.
 */
class Util {


	/**
	 * Include with variables.
	 *
	 * This function extends include() by adding the ability to pass variables to the included file.
	 *
	 * $variables is made available for use in the included file.
	 */
	public static function include_with_vars( $template_path, $variables = array() ) {
		if ( file_exists( $template_path ) ) {
			// Start output buffering.
			ob_start();
			// Include the template file.
			include $template_path;
			// End buffering and return its contents.
			$output = ob_get_clean();
			return $output;

		} else {
			error_log( 'Error Monitor Error: $template_path not found.' );
			return false;
		}
	}


	/**
	 * Retrieve file contents the 'WordPress way'.
	 *
	 * @param string $path File system path.
	 */
	public static function get_contents( $path ) {
		include_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		if ( ! class_exists( 'WP_Filesystem_Direct' ) ) {
			return false;
		}
		$wp_filesystem = new \WP_Filesystem_Direct( null );
		$string        = $wp_filesystem->get_contents( $path );
		return $string;
	}
}
