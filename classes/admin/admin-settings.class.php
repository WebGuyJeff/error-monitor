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
	 * Plugin settings.
	 */
	private $settings = array();


	/**
	 * Parent menu item and dashboard.
	 */
	private $settings_parent;


	/**
	 * Settings tab: Main.
	 */
	private $monitor_tab;


	/**
	 * Settings tab: Email.
	 */
	private $email_tab;


	/**
	 * Settings tab: Logs.
	 */
	private $logs_tab;


	/**
	 * Settings tab: Log File.
	 */
	private $log_file_tab;


	/**
	 * Setup the class.
	 */
	public function __construct() {
		$this->settings_parent = new Admin_Settings_Parent();
		$this->monitor_tab     = new Settings_Page_Monitor();
		$this->email_tab       = new Settings_Page_Email();
		$this->logs_tab        = new Settings_Page_Logs();
		$this->log_file_tab    = new Settings_Page_Log_File();
		$this->register();
		$this->settings = Settings::get();
	}


	/**
	 * Page URLs, slugs and titles.
	 */
	public static function url( $page, $part = false ): string {

		$plugin_url = admin_url( 'admin.php?page=webguyjeff-error-monitor' );

		$url = array(
			'base' => array(
				'url'   => $plugin_url,
				'slug'  => 'webguyjeff-error-monitor',
				'title' => 'Monitor',
			),
			'logs' => array(
				'url'   => $plugin_url . '&tab=logs',
				'slug'  => 'logs',
				'title' => 'Logs',
				'query' => '&tab=logs',
			),
			'email' => array(
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
			$this->settings_parent::$page_slug, // parent_slug.
			self::PLUGINNAME,                         // page_title.
			self::PLUGINNAME,                         // menu_title.
			'manage_options',                         // capability.
			self::url( 'base', 'slug' ),             // menu_slug.
			array( &$this, 'create_settings_page' ),  // function.
			null,                                     // position.
		);
	}


	/**
	 * Echo a link to this plugin's settings page.
	 */
	public function echo_plugin_settings_link() {
		?>
		<a href="/wp-admin/admin.php?page=<?php echo esc_html( self::url( 'base', 'slug' ) ); ?>">
			<?php echo self::PLUGINNAME; ?>
		</a>
		<?php
	}


	/**
	 * Create Plugin Settings Page
	 */
	public function create_settings_page() {

		wp_enqueue_script( 'error_monitor_admin_js' );
		wp_enqueue_style( 'error_monitor_admin_css' );
		$email_configured = Settings::email_configured();
		$cron_scheduled   = Cron_Service::cron_scheduled();
		$header           = Util::include_with_vars(
			ERRORMONITOR_PATH . 'classes/admin/parts/admin-header.php',
			array(
				self::PLUGINNAME,
				$email_configured,
				$cron_scheduled,
				$this->settings['last_scan_time'],
				$this->settings['last_log_timestamp'],
			),
		);
		?>

		<div class="wrap adminPage">

			<?php echo $header; ?>

			<div class="adminPage_body">

				<?php
				// Get the active tab from the $_GET URL param.
				$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : null;
				?>

				<div class="adminPage_container">
					<nav class="adminPage_nav">
						<a
							href="<?php echo esc_attr( self::url( 'base', 'url' ) ); ?>"
							class="nav-tab<?php echo ( null === $tab ) ? esc_attr( ' nav-tab-active' ) : ''; ?>"
						><?php echo esc_html( self::url( 'base', 'title' ) ); ?></a>
						<a
							href="<?php echo esc_attr( self::url( 'logs', 'url' ) ); ?>"
							class="nav-tab<?php echo ( self::url( 'logs', 'slug' ) === $tab ) ? esc_attr( ' nav-tab-active' ) : ''; ?>"
						><?php echo esc_html( self::url( 'logs', 'title' ) ); ?></a>
						<a
							href="<?php echo esc_attr( self::url( 'email', 'url' ) ); ?>"
							class="nav-tab<?php echo ( self::url( 'email', 'slug' ) === $tab ) ? esc_attr( ' nav-tab-active' ) : ''; ?>"
						><?php echo esc_html( self::url( 'email', 'title' ) ); ?></a>
						<a
							href="<?php echo esc_attr( self::url( 'log-file', 'url' ) ); ?>"
							class="nav-tab<?php echo ( self::url( 'log-file', 'slug' ) === $tab ) ? esc_attr( ' nav-tab-active' ) : ''; ?>"
						><?php echo esc_html( self::url( 'log-file', 'title' ) ); ?></a>
					</nav>
				</div>

				<div class="tab_content">
					<?php
					switch ( $tab ) :
						default:
							$this->monitor_tab->output();
							break;
						case self::url( 'logs', 'slug' ):
							$this->logs_tab->output();
							break;
						case self::url( 'email', 'slug' ):
							$this->email_tab->output();
							break;
						case self::url( 'log-file', 'slug' ):
							$this->log_file_tab->output();
							break;
					endswitch;
					?>
				</div>

			</div>
		</div>

		<?php
	}
}
