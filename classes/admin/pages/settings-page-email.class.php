<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Settings Tab 1.
 *
 * @package error-monitor
 */
class Settings_Page_Email {

	public const PAGE   = 'error_monitor_page_email';
	public const GROUP  = 'error_monitor_group_email';
	public const OPTION = 'error_monitor_settings';

	/**
	 * Settings retrieved from database.
	 */
	private $settings;

	/**
	 * The pages.
	 */
	private $pages = array();

	/**
	 * Saved custom metadata from the database.
	 */
	private $meta = array();


	/**
	 * Hook into WP.
	 */
	public function __construct() {
		$this->settings = get_option( self::OPTION );
		add_action( 'admin_init', array( &$this, 'register' ), 11, 0 );
	}


	/**
	 * Register the settings.
	 */
	public function register() {
		$sanitise = new Sanitise();
		register_setting(
			self::GROUP,
			self::OPTION,
			array( 'sanitize_callback' => array( $sanitise, 'all_settings' ) ),
		);
		$this->register_sections();
	}


	/**
	 * Output the content for this tab.
	 */
	public function output() {
		?>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::GROUP );
				do_settings_sections( self::PAGE );
				submit_button( 'Save Settings' );
				?>

				<hr>

				<h2><?php esc_html_e( 'Test Settings', 'error-monitor' ); ?></h2>
				<p><?php esc_html_e( 'Save your settings before testing connection and sending a test email.', 'error-monitor' ); ?></p>

				<div class="errorMonitor__testWrapper">
					<div class="adminButtonRow">

						<button
							type="button"
							id="errorMonitor__smtpTest_button"
							class="button button-secondary"
							data-test="smtp"
							disabled
						>
							<span class="errorMonitor__submitLabel-ready">
								<?php _e( 'Test Connection', 'error_monitor' ); ?>
							</span>
							<span class="errorMonitor__submitLabel-notReady">
								<?php _e( 'Test Connection [Check config]', 'error_monitor' ); ?>
							</span>
						</button>

						<button
							type="button"
							id="errorMonitor__emailTest_button"
							class="button button-secondary"
							data-test="email"
							disabled
						>
							<span class="errorMonitor__submitLabel-ready">
								<?php _e( 'Send Test Email', 'error_monitor' ); ?>
							</span>
							<span class="errorMonitor__submitLabel-notReady">
								<?php _e( 'Send Test Email [Check config]', 'error_monitor' ); ?>
							</span>
						</button>

					</div>

					<div class='errorMonitor__alertsContainer' style="display:none;">
						<div class='errorMonitor__alerts'></div>
					</div>

				</div>

			</form>
		<?php
	}


	/**
	 * Register meta settings section and fields.
	 */
	private function register_sections() {
		$section = 'section_smtp_settings';
		add_settings_section( $section, 'SMTP Settings', array(), self::PAGE );
			add_settings_field( 'username', 'Username', array( &$this, 'echo_field_username' ), self::PAGE, $section );
			add_settings_field( 'password', 'Password', array( &$this, 'echo_field_password' ), self::PAGE, $section );
			add_settings_field( 'host', 'Host', array( &$this, 'echo_field_host' ), self::PAGE, $section );
			add_settings_field( 'port', 'Port', array( &$this, 'echo_field_port' ), self::PAGE, $section );

		$section = 'section_sending';
		add_settings_section( $section, 'Message Sending', array( &$this, 'echo_intro_section_sending' ), self::PAGE );
			add_settings_field( 'from_email', 'Sent-from email address', array( &$this, 'echo_field_from_email' ), self::PAGE, $section );
			add_settings_field( 'to_email', 'Email to send notifications to', array( &$this, 'echo_field_to_email' ), self::PAGE, $section );
	}


	/**
	 * Output Field - SMTP Settings - Username
	 */
	public function echo_field_username() {
		$setting = self::OPTION . '[username]';
		printf(
			'<input class="regular-text" type="text" id="%s" name="%s" value="%s">',
			$setting,
			$setting,
			$this->settings['username'] ?? ''
		);
	}


	/**
	 * Output Field - SMTP Settings - Password
	 */
	public function echo_field_password() {
		$setting = self::OPTION . '[password]';
		printf(
			'<input class="regular-text hideWhenOauthEnabled" type="password" id="%s" name="%s" value="%s">',
			$setting,
			$setting,
			$this->settings['password'] ?? ''
		);
	}


	/**
	 * Output Field - SMTP Settings - Host
	 */
	public function echo_field_host() {
		$setting = self::OPTION . '[host]';
		printf(
			'<input class="regular-text" type="text" id="%s" name="%s" value="%s">',
			$setting,
			$setting,
			$this->settings['host'] ?? ''
		);
	}


	/**
	 * Output Field - SMTP Settings - Port
	 */
	public function echo_field_port() {
		$setting = self::OPTION . '[port]';
		printf(
			'<input class="regular-text" type="number" min="25" max="2525" step="1" id="%s" name="%s" value="%s">',
			$setting,
			$setting,
			$this->settings['port'] ?? ''
		);
	}


	/**
	 * Output Intro - Message Sending
	 */
	public function echo_intro_section_sending() {
		?>
			<ul class="adminInstructionsList">
				<li>
					The <code>sent from</code> email should match your website domain to improve
					deliverability.
				</li>
				<li>
					Ensure DNS is configured with <strong>DMARC</strong>, <strong>SPF</strong>, and
					<strong>DKIM</strong> so the <code>sent from</code> domain can be authenticated
					to improve deliverability.
				</li>
			</ul>
		<?php
	}


	/**
	 * Output Field - Message Sending - From Email
	 */
	public function echo_field_from_email() {
		$setting = self::OPTION . '[from_email]';
		printf(
			'<input class="regular-text" type="email" id="%s" name="%s" value="%s">',
			$setting,
			$setting,
			$this->settings['from_email'] ?? get_bloginfo( 'admin_email' )
		);
	}


	/**
	 * Output Field - Message Sending - To Email
	 */
	public function echo_field_to_email() {
		$setting = self::OPTION . '[to_email]';
		printf(
			'<input class="regular-text" type="email" id="%s" name="%s" value="%s">',
			$setting,
			$setting,
			$this->settings['to_email'] ?? get_bloginfo( 'admin_email' )
		);
	}
}