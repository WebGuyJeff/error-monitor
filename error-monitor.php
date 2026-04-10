<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Plugin Name: Error Monitor
 * Plugin URI: https://github.com/WebGuyJeff/error-monitor
 * Description: Get notified about new errors on your WordPress site.
 * Version: 0.5.1
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Web Guy Jeff
 * Author URI: https://webguyjeff.com
 * License: GPL-2.0+
 *
 * @package error-monitor
 */

// Set global constants.
define( 'ERRORMONITOR_PATH', trailingslashit( __DIR__ ) );
define( 'ERRORMONITOR_URL', trailingslashit( get_site_url( null, strstr( __DIR__, '/wp-content/' ) ) ) );

// Register namespaced autoloader.
$namespace = 'WebGuyJeff\\Error_Monitor\\';
$root      = ERRORMONITOR_PATH . 'classes/';
require_once $root . 'autoload.php';

// Register the plugin cron.
require_once __DIR__ . '/classes/cron/cron-hooks.class.php';
new Cron_Hooks();
register_activation_hook( __FILE__, array( Cron_Hooks::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Cron_Hooks::class, 'deactivate' ) );

// Setup the plugin.
$init = new Setup_Plugin();
