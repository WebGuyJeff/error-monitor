<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Error Monitor - Initialisation.
 *
 * Setup styles and helper functions for this plugin.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */
class Init {

	/**
	 * Store if this is admin screen check.
	 *
	 * @var bool $is_admin
	 */
	private bool $is_admin;

	/**
	 * Setup the class.
	 */
	public function __construct() {
		$this->is_admin = is_admin() ? true : false;
	}


	/**
	 * Setup the plugin.
	 */
	public function setup() {
		( new Settings_Registration() )->register();
		$this->maybe_create_log_table();
		if ( $this->is_admin ) {
			new Admin_Settings();
			( new Log_File_Discovery() )->maybe_bootstrap_setting();
		}
		add_action( 'rest_api_init', array( $this, 'register_rest_api_routes' ), 10, 0 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_and_styles' ), 10, 1 );
	}


	/**
	 * Register admin scripts and styles.
	 */
	public function admin_scripts_and_styles( $hook_suffix ) {
		wp_register_style( 'error_monitor_admin_css', ERRORMONITOR_URL . 'build/admin/css/error-monitor-admin.css', array(), filemtime( ERRORMONITOR_PATH . 'build/admin/css/error-monitor-admin.css' ), 'all' );
		wp_register_script( 'error_monitor_admin_js', ERRORMONITOR_URL . 'build/admin/js/error-monitor-admin.js', array(), filemtime( ERRORMONITOR_PATH . 'build/admin/js/error-monitor-admin.js' ), false );
		wp_add_inline_script( 'error_monitor_admin_js', Inline_Script::get_variables(), 'before' );
		if ( ! wp_script_is( 'webguyjeff_icons', 'registered' ) ) {
			wp_register_style( 'webguyjeff_icons', ERRORMONITOR_URL . 'dashicons/css/webguyjeff-icons.css', array(), filemtime( ERRORMONITOR_PATH . 'dashicons/css/webguyjeff-icons.css' ), 'all' );
		}
		if ( ! wp_script_is( 'webguyjeff_icons', 'enqueued' ) ) {
			wp_enqueue_style( 'webguyjeff_icons' );
		}
	}


	/**
	 * Register rest api routes.
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_rest_route/
	 */
	public function register_rest_api_routes() {
		register_rest_route(
			'webguyjeff/error-monitor/v1',
			'/action',
			array(
				'methods'             => 'POST',
				'callback'            => array( new Action_Controller(), 'handle' ),
				'permission_callback' => '__return_true',
			)
		);
	}


	/**
	 * Create or update the logs table.
	 */
	public function maybe_create_log_table() {

		global $wpdb;

		$table_name      = $wpdb->prefix . 'error_monitor_logs';
		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			hash CHAR(32) NOT NULL,
			message LONGTEXT NOT NULL,
			normalized_message LONGTEXT NOT NULL,
			severity VARCHAR(20) NOT NULL,
			timestamps LONGTEXT NOT NULL,
			count INT UNSIGNED NOT NULL DEFAULT 1,
			first_seen INT UNSIGNED NOT NULL,
			last_seen INT UNSIGNED NOT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY hash (hash),
			KEY severity (severity),
			KEY last_seen (last_seen)
		) $charset_collate;";

		dbDelta( $sql );
	}
}
