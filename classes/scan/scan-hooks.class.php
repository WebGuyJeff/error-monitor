<?php
namespace WebGuyJeff\Error_Monitor;

class Scan_Hooks {

	/**
	 * Setup.
	 */
	public function __construct() {

		$this->hooks();
	}


	/**
	 * Hooks.
	 *
	 * @return void
	 */
	private function hooks(): void {
		add_action( 'admin_post_error_monitor_manual_scan', array( $this, 'run_manual_scan' ) );

	}


	/**
	 * Run manual scan.
	 *
	 * This is triggered by the admin UI.
	 */
	public function run_manual_scan() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}

		if ( ! isset( $_POST['_em_nonce'] ) || ! wp_verify_nonce( $_POST['_em_nonce'], 'error_monitor_manual_scan' ) ) {
			wp_die( 'Invalid nonce' );
		}

		$controller = new \WebGuyJeff\Error_Monitor\Scan_Controller();
		$result     = $controller->run( true );

		// Store result temporarily (transient)
		set_transient( 'error_monitor_manual_result', $result, 30 );

		// Redirect back to settings page
		wp_redirect( admin_url( 'admin.php?page=webguyjeff-error-monitor' ) );
		exit;
	}
}