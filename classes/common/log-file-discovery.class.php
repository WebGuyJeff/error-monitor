<?php
namespace WebGuyJeff\Error_Monitor;

class Log_File_Discovery {

	/**
	 * Get resolved log file path.
	 */
	public function get_log_file_path(): ?string {

		$custom = Settings::get( 'log_file_path' );

		if ( is_string( $custom ) && trim( $custom ) !== '' ) {
			return $custom;
		}

		// fallback discovery.
		return $this->auto_discover();
	}

	/**
	 * Get full status.
	 */
	public function get_status(): array {

		$path = $this->get_log_file_path();

		return array(
			'path'     => $path,
			'exists'   => (bool) ( $path && file_exists( $path ) ),
			'readable' => (bool) ( $path && is_readable( $path ) ),
			'writable' => (bool) ( $path && is_writable( $path ) ),
			'source'   => $this->detect_source( $path ),

			'wp_debug'         => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'wp_debug_log'     => defined( 'WP_DEBUG_LOG' ) ? WP_DEBUG_LOG : false,
			'wp_debug_display' => defined( 'WP_DEBUG_DISPLAY' ) ? WP_DEBUG_DISPLAY : false,

			'php_error_log'    => ini_get( 'error_log' ),
		);
	}

	/**
	 * Detect source.
	 */
	private function detect_source( ?string $path ): string {

		if ( empty( $path ) ) {
			return 'none';
		}

		$wp_path = null;

		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			$wp_path = is_string( WP_DEBUG_LOG ) ? WP_DEBUG_LOG : WP_CONTENT_DIR . '/debug.log';
		}

		$php_path = ini_get( 'error_log' );

		// WordPress log.
		if ( $wp_path && $path === $wp_path ) {
			return 'WordPress';
		}

		// PHP ini log.
		if ( $php_path && $path === $php_path ) {
			return 'php.ini';
		}

		// Custom (only if user explicitly set it).
		$custom = Settings::get( 'log_file_path' );

		if ( $custom && $path === $custom ) {
			return 'custom';
		}

		return 'unknown';
	}

	/**
	 * Auto discover best log file.
	 */
	public function auto_discover(): ?string {

		$candidates = array();

		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			$candidates[] = is_string( WP_DEBUG_LOG ) ? WP_DEBUG_LOG : WP_CONTENT_DIR . '/debug.log';
		}

		$ini = ini_get( 'error_log' );
		if ( $ini ) {
			$candidates[] = $ini;
		}

		foreach ( $candidates as $file ) {
			if ( $file && file_exists( $file ) && is_readable( $file ) ) {
				return $file;
			}
		}

		return null;
	}

	/**
	 * Bootstrap setting if empty.
	 */
	public function maybe_bootstrap_setting(): void {

		$current = Settings::get( 'log_file_path' );

		if ( ! empty( $current ) ) {
			return;
		}

		$found = $this->auto_discover();

		if ( $found ) {
			Settings::set( 'log_file_path', $found );
		}
	}

	/**
	 * Force discover and overwrite.
	 */
	public function force_discover_and_save(): bool {

		$found = $this->auto_discover();

		if ( $found ) {
			return Settings::set( 'log_file_path', $found );
		}

		return false;
	}
}