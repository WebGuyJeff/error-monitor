<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * WebGuyJeff Custom Fields - Admin Settings Parent.
 *
 * Check to see if the WebGuyJeff parent admin settings page already exisits and
 * if not, create it. A hook is created for child pages to add to this parent.
 * This class should be used accross all WebGuyJeff plugins and themes.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */

class Admin_Settings_Parent {


	/**
	 * Settings page slug to add with add_submenu_page().
	 */
	public $admin_label = 'WebGuyJeff';


	/**
	 * Settings page slug to add with add_submenu_page().
	 */
	public static $page_slug = 'webguyjeff';


	/**
	 * Settings group name called by settings_fields().
	 *
	 * To add multiple sections to the same settings page, all settings
	 * registered for that page MUST BE IN THE SAME 'OPTION GROUP'.
	 */
	public $group_name = 'group_webguyjeff_settings';


	/**
	 * Init the class by hooking into the admin interface.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( &$this, 'register_admin_menu' ), 1 );
	}


	/**
	 * Add admin menu option to sidebar
	 */
	public function register_admin_menu() {

		// Add WebGuyJeff parent menu, if it doesn't exist.
		$parent_menu = menu_page_url( self::$page_slug, false );
		if ( false === (bool) $parent_menu ) {
			add_menu_page(
				$this->admin_label . ' Settings', // page_title
				$this->admin_label,               // menu_title
				'manage_options',                 // capability
				self::$page_slug,                 // menu_slug
				array( &$this, 'create_parent_page' ), // function
				'dashicons-webguyjeff-icon',           // icon_url
				4                                 // position
			);
		}
	}


	/**
	 * Do Action Hook
	 */
	public function webguyjeff_settings_dashboard_entry() {
		do_action( 'webguyjeff_settings_dashboard_entry' );
	}


	/**
	 * Create WebGuyJeff Settings Page
	 */
	public function create_parent_page() {
		?>

		<div class="wrap">
			<h1>
				<span class="dashicons-webguyjeff-logo" style="font-size: 2em; margin-right: 0.2em;"></span>
				WebGuyJeff Settings
			</h1>

			<?php $this->webguyjeff_settings_dashboard_entry(); ?>

		</div>

		<?php
	}
}//end class
