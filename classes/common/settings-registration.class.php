<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Register plugin settings.
 */
class Settings_Registration {

	/**
	 * Register settings.
	 */
	public function register(): void {

		register_setting(
			'error_monitor_group',
			'error_monitor_settings',
			array(
				'sanitize_callback' => array( new Settings_Sanitise(), 'all_settings' ),
			)
		);
	}
}
