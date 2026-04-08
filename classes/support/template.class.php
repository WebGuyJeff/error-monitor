<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Template hanlding utilities.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright 2023 Jefferson Real
 */

class Template {

	/**
	 * Include with variables.
	 *
	 * This function extends include() by adding the ability to pass variables to the included file.
	 *
	 * $variables is made available for use in the included file.
	 */
	public static function include_with_vars( $template_path, $vars ) {
		if ( file_exists( $template_path ) ) {
			$variables = $vars;
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
}
