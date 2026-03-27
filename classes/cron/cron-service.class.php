<?php
namespace WebGuyJeff\Error_Monitor;

class Cron_Service {


	/**
	 * Schedule event.
	 */
	public function schedule_event( int $frequency_mins ): void {

		$schedule = 'error_monitor_' . $frequency_mins . '_min';

		$timestamp = wp_next_scheduled( 'error_monitor_scan_logs' );

		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'error_monitor_scan_logs' );
		}

		wp_schedule_event( time(), $schedule, 'error_monitor_scan_logs' );
	}


	/**
	 * Unschedule event.
	 */
	public function unschedule_event(): void {

		$timestamp = wp_next_scheduled( 'error_monitor_scan_logs' );

		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'error_monitor_scan_logs' );
		}
	}


	/**
	 * Execute.
	 */
	public function execute(): void {

// DEBUG.
error_log( 'CRON execute()' );

		$controller = new Scan_Controller();
		$controller->run( false );
	}


	/**
	 * Check if the cron is scheduled.
	 *
	 * @return boolean
	 */
	public static function cron_scheduled(): bool {
		return (bool) wp_next_scheduled( 'error_monitor_scan_logs' );
	}
}