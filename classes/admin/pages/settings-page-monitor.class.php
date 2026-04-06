<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Settings Tab Monitor.
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
	 * Setup class.
	 */
	public function __construct() {
		$this->settings = Settings::get();
	}


	/**
	 * Output the content for this tab.
	 */
	public function output() {
		?>
		<div class="adminPage_container">
			<form>
				
				<h2>Notifications</h2>
				<p>A notification email will only be sent if new logs are found.</p>

				<?php

				$setting = 'monitor_enabled';
				Get_Input::toggle(
					array(
						'setting'     => $setting,
						'wp_option'   => self::OPTION,
						'value'       => isset( $this->settings[ $setting ] ) ? (bool) $this->settings[ $setting ] : '',
						'label'       => __( 'Enable scheduled monitoring', 'error-monitor' ),
						'description' => __( 'Disable scheduled log scanning and alerts. Manual scans will still work.', 'error-monitor' ),
					)
				);

				$setting = 'scan_frequency_mins';
				Get_Input::input(
					array(
						'setting'     => $setting,
						'wp_option'   => self::OPTION,
						'value'       => $this->settings[ $setting ] ?? '',
						'label'       => $label = __( 'Scan Frequency (mins)', 'error-monitor' ),
						'description' => $description = __( 'A scan will be performed at these intervals', 'error-monitor' ),
						'type'        => 'number',
						'atttributes' => array(
							'step' => '1',
							'min'  => '1',
							'max'  => '60',
						),
						'classes'     => 'field-small',
					)
				);

				?>
				<hr>
				<h2>Log History</h2>
				<?php

				$setting = 'log_retention_days';
				Get_Input::select(
					array(
						'setting'     => $setting,
						'wp_option'   => self::OPTION,
						'value'       => isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : '',
						'options'     => array(
							'7'   => '7 days',
							'30'  => '30 days',
							'90'  => '3 months',
							'180' => '6 months',
							'365' => '12 months',
						),
						'label'       => __( 'Log retention (days)', 'error-monitor' ),
						'description' => __( 'Logs older than the selected period will be automatically deleted to prevent database bloat.', 'error-monitor' ),
						'classes'     => 'field-small',
					)
				);
				?>

				<hr>

				<h2><?php esc_html_e( 'Manual Scan', 'error-monitor' ); ?></h2>
				<p><?php esc_html_e( 'Run a scan immediately and get emailed the results.', 'error-monitor' ); ?></p>

				<?php
				Get_Input::action_buttons(
					array(
						array(
							'action'               => 'manual_scan',
							'label'                => __( 'Run Scan Now', 'error-monitor' ),
							'primary_or_secondary' => 'primary',
						),
					),
				);
				?>

			</form>

		</div>
		<?php
	}
}
