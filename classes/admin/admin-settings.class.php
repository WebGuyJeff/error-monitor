<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Admin Settings.
 *
 * Hook into the WP admin area and add menu options and settings pages.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2024, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */
class Admin_Settings {


	/**
	 * Settings page menu title to add with add_submenu_page().
	 */
	private const PLUGINNAME = 'Error Monitor';

	/**
	 * Parent menu item and dashboard.
	 */
	private $settings_parent;


	/**
	 * Setup the class.
	 */
	public function __construct() {
		$this->settings_parent = new Admin_Settings_Parent();
		$this->register();
	}


	/**
	 * Page URLs, slugs and titles.
	 */
	public static function url( $page, $part = false ): string {

		$plugin_url = admin_url( 'admin.php?page=webguyjeff-error-monitor' );

		$url = array(
			'base'     => array(
				'url'   => $plugin_url,
				'slug'  => 'webguyjeff-error-monitor',
				'title' => 'Monitor',
			),
			'logs'     => array(
				'url'   => $plugin_url . '&tab=logs',
				'slug'  => 'logs',
				'title' => 'Logs',
				'query' => '&tab=logs',
			),
			'email'    => array(
				'url'   => $plugin_url . '&tab=email',
				'slug'  => 'email',
				'title' => 'Email Account',
				'query' => '&tab=email',
			),
			'log-file' => array(
				'url'   => $plugin_url . '&tab=log-file',
				'slug'  => 'log-file',
				'title' => 'Log File',
				'query' => '&tab=log-file',
			),
		);

		return $url[ $page ][ $part ];
	}


	/**
	 * Register the admin menu and settings pages.
	 */
	public function register() {
		add_action( 'admin_menu', array( &$this->settings_parent, 'register_admin_menu' ), 1, 0 );
		add_action( 'webguyjeff_settings_dashboard_entry', array( &$this, 'echo_plugin_settings_link' ), 10, 0 );
		add_action( 'admin_menu', array( &$this, 'register_admin_menu' ), 99 );
	}


	/**
	 * Add admin menu option to sidebar
	 */
	public function register_admin_menu() {
		add_submenu_page(
			$this->settings_parent::$page_slug,      // parent_slug.
			self::PLUGINNAME,                        // page_title.
			self::PLUGINNAME,                        // menu_title.
			'manage_options',                        // capability.
			self::url( 'base', 'slug' ),             // menu_slug.
			array( &$this, 'create_settings_page' ), // function.
			null,                                    // position.
		);
	}


	/**
	 * Echo a link to this plugin's settings page.
	 */
	public function echo_plugin_settings_link() {
		?>
		<a href="/wp-admin/admin.php?page=<?php echo esc_html( self::url( 'base', 'slug' ) ); ?>">
			<?php echo esc_html( self::PLUGINNAME ); ?>
		</a>
		<?php
	}


	/**
	 * Create Plugin Settings Page
	 */
	public function create_settings_page() {

		wp_enqueue_script( 'error_monitor_admin_js' );
		wp_enqueue_style( 'error_monitor_admin_css' );

		?>
		<div id="errorMonitorReactRoot" class="wrap adminPage"></div>
		<?php
	}
}
