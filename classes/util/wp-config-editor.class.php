<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Handle wp-config.php updates.
 */
class WP_Config_Editor {

	/**
	 * Update constants in wp-config.php.
	 */
	public function update( array $constant ): bool {

		$const = key( $constant );
		$value = current( $constant );

		$file = ABSPATH . 'wp-config.php';

		if ( ! file_exists( $file ) || ! is_writable( $file ) ) {
			return false;
		}

		if ( ! file_exists( $file . '.bak' ) ) {
			copy( $file, $file . '.bak' );
		}

		$content = file_get_contents( $file );

		if ( false === $content ) {
			return false;
		}

		$anchor = "/* That's all, stop editing!";

		$name    = strtoupper( str_replace( 'wp_', 'WP_', $const ) );
		$escaped = preg_quote( $name, '/' );

		$pattern = "/define\s*\(\s*'{$escaped}'\s*,\s*.*?\)\s*;/i";
		$replace = "define( '{$name}', " . ( $value ? 'true' : 'false' ) . ' );';

		if ( preg_match( $pattern, $content ) ) {
			$content = preg_replace( $pattern, $replace, $content, 1 );
		} else {
			$content = str_replace( $anchor, $replace . "\n" . $anchor, $content );
		}

		return false !== file_put_contents( $file, $content );
	}
}
