<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Plugin Name: WebGuyJeff: Error Monitor
 * Plugin URI: https://webguyjeff.com
 * Description: Monitor the error log and get notifications about new errors on your WordPress site.
 * Version: 0.0.1
 * Author: Jefferson Real
 * Author URI: https://webguyjeff.com
 * License: GPL2
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
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
$init = new Init();
$init->setup();
