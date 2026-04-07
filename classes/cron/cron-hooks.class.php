<?php
namespace WebGuyJeff\Error_Monitor;

class Cron_Hooks {

	private Cron_Service $service;

	/**
	 * Setup.
	 */
	public function __construct() {

		$this->service = new Cron_Service();
		$this->hooks();
	}


	/**
	 * Hooks.
	 *
	 * @return void
	 */
	private function hooks(): void {
		add_filter( 'cron_schedules', array( $this, 'add_dynamic_schedule' ) );
		add_action( 'update_option_error_monitor_settings', array( $this, 'reschedule' ), 10, 2 );
		add_action( 'error_monitor_scan_logs', array( $this, 'execute' ) );
	}


	/**
	 * Add a dynamic cron schedule.
	 *
	 * @param  array $schedules - WordPress cron schedules.
	 * @return array $schedules - Updated schedules.
	 */
	public function add_dynamic_schedule( $schedules ): array {

		$minutes = (int) Settings::get( 'scan_frequency_mins' );
		$minutes = $minutes > 0 && $minutes <= 60 ? $minutes : 30;

		$key = 'error_monitor_' . $minutes . '_min';

		$schedules[ $key ] = array(
			'interval' => $minutes * 60,
			'display'  => sprintf(
				/* translators: %d: Number of minutes between scans. */
				__( 'Every %d Minutes (Error Monitor)', 'error-monitor' ),
				$minutes
			),
		);

		return $schedules;
	}


	/**
	 * Reschedule event if frequency changed.
	 *
	 * @param array $old - Old settings.
	 * @param array $new - New settings.
	 * @return void
	 */
	public function reschedule( $old, $new ) {

		$old_mins = isset( $old['scan_frequency_mins'] ) ? (int) $old['scan_frequency_mins'] : 0;
		$new_mins = isset( $new['scan_frequency_mins'] ) ? (int) $new['scan_frequency_mins'] : 0;

		if ( $old_mins !== $new_mins ) {
			$this->service->schedule_event( $new_mins );
		}
	}


	/**
	 * Execute cron task.
	 *
	 * @return void
	 */
	public function execute(): void {
		$this->service->execute();
	}


	/**
	 * Activation hook.
	 *
	 * Called from main plugin file ONLY e.g:
	 * register_activation_hook( __FILE__, array( Cron_Hooks::class, 'activate' ) );
	 */
	public static function activate(): void {

		$minutes = (int) Settings::get( 'scan_frequency_mins' );
		$minutes = $minutes > 0 ? $minutes : 30;

		$service = new Cron_Service();
		$service->schedule_event( $minutes );
	}


	/**
	 * Deactivation hook.
	 *
	 * Called from main plugin file ONLY e.g:
	 * register_deactivation_hook( __FILE__, array( Cron_Hooks::class, 'deactivate' ) );
	 */
	public static function deactivate(): void {

		$service = new Cron_Service();
		$service->unschedule_event();
	}
}
