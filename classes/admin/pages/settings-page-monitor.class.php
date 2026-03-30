<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Settings Tab 1.
 *
 * @package error-monitor
 */
class Settings_Page_Monitor {

	public const PAGE   = 'error_monitor_page_monitor';
	public const GROUP  = 'error_monitor_group_monitor';
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
		<div class="adminPage_container">
			<form method="post" action="options.php">
				<?php
					settings_fields( self::GROUP );
					do_settings_sections( self::PAGE );
					submit_button( 'Save Settings' );
				?>
			</form>

			<hr>

			<h2><?php esc_html_e( 'Manual Scan', 'error-monitor' ); ?></h2>
			<p><?php esc_html_e( 'Run a scan immediately and get emailed the results.', 'error-monitor' ); ?></p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="error_monitor_manual_scan">
				<?php wp_nonce_field( 'error_monitor_manual_scan', '_em_nonce' ); ?>
				<div class="adminButtonRow">
					<?php submit_button( 'Run Scan Now', 'secondary', 'run_scan', false ); ?>
				</div>
			</form>

			<?php
			$result = get_transient( 'error_monitor_manual_result' );

			if ( $result ) {

				delete_transient( 'error_monitor_manual_result' );

				$class = ! empty( $result['success'] ) ? 'notice-success' : 'notice-error';

				$count = isset( $result['total'] )
					? (int) $result['total']
					: ( isset( $result['count'] ) ? (int) $result['count'] : 0 );

				printf(
					'<div class="notice %s is-dismissible"><p>%s</p><p><strong>%s:</strong> %d</p></div>',
					esc_attr( $class ),
					esc_html( $result['message'] ?? '' ),
					esc_html__( 'Logs found', 'error-monitor' ),
					$count
				);
			}
			?>

		</div>
		<?php
	}


	/**
	 * Register sections.
	 */
	private function register_sections() {

		$section = 'section_notifications';
		add_settings_section( $section, 'Notifications', array( &$this, 'echo_section_intro_notifications' ), self::PAGE );
		add_settings_field( 'scan_frequency_mins', 'Scan Frequency (mins)', array( &$this, 'echo_field_scan_frequency_mins' ), self::PAGE, $section );
		add_settings_field( 'last_scan_time', 'Last Scan Time', array( &$this, 'echo_field_last_scan_time' ), self::PAGE, $section );
		add_settings_field( 'last_log_timestamp', 'Last Log Timestamp', array( &$this, 'echo_field_last_log_timestamp' ), self::PAGE, $section );

		$section = 'section_log_history';
		add_settings_section( $section, 'Log History', array( &$this, 'echo_section_intro_log_history' ), self::PAGE );
		add_settings_field( 'log_retention_days', 'Log Retention Period', array( &$this, 'echo_field_log_retention_days' ), self::PAGE, $section );
	}


	/**
	 * Output Intro - Notifications
	 */
	public function echo_section_intro_notifications() {
		echo '<p>A scan will be performed at these intervals. A notification email will only be sent if new logs are found.</p>';
	}


	/**
	 * Output Field - Notifications - Scan Frequency
	 */
	public function echo_field_scan_frequency_mins() {
		$setting = self::OPTION . '[scan_frequency_mins]';
		printf(
			'<input type="number" min="1" max="60" step="1" id="%s" name="%s" value="%s">',
			$setting,
			$setting,
			$this->settings['scan_frequency_mins'] ?? '30',
		);
	}


	/**
	 * Output Field - Notifications - Last Scan Time
	 */
	public function echo_field_last_scan_time() {
		$setting   = self::OPTION . '[last_scan_time]';
		$value     = $this->settings['last_scan_time'] ?? '0';
		$timestamp = new Timestamp();
		printf(
			'<input type="number" id="%s" name="%s" value="%s" hidden><span>%s</span>',
			$setting,
			$setting,
			$value,
			$value > 0 ? $timestamp::unix_to_readable( $value ) : 'Never',
		);
	}


	/**
	 * Output Field - Notifications - Last Log Timestamp
	 */
	public function echo_field_last_log_timestamp() {
		$setting   = self::OPTION . '[last_log_timestamp]';
		$value     = $this->settings['last_log_timestamp'] ?? '0';
		$timestamp = new Timestamp();
		printf(
			'<input type="number" id="%s" name="%s" value="%s" hidden><span>%s</span>',
			$setting,
			$setting,
			$value,
			$value > 0 ? $timestamp::unix_to_readable( $value ) : 'Never',
		);
	}


	/**
	 * Output Intro - Log History
	 */
	public function echo_section_intro_log_history() {
		echo '<p>Logs older than the selected period will be automatically deleted to prevent database bloat.</p>';
	}


	/**
	 * Output Field - Log History - Log retention days
	 */
	public function echo_field_log_retention_days() {
		$setting = self::OPTION . '[log_retention_days]';
		$value   = $this->settings['log_retention_days'] ?? '30';
		$options = array(
			'7'   => '7 days',
			'30'  => '30 days',
			'90'  => '3 months',
			'180' => '6 months',
			'365' => '12 months',
		);
		echo '<select id="' . esc_attr( $setting ) . '" name="' . esc_attr( $setting ) . '">';
		foreach ( $options as $key => $label ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $key ),
				selected( $value, $key, false ),
				esc_html( $label )
			);
		}
		echo '</select>';
	}
}
