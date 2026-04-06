<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Settings Tab Email.
 *
 * @package error-monitor
 */
class Settings_Page_Email {

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
	 * Output markup.
	 */
	public function output() {
		?>
		<div class="adminPage_container">
			<form>

				<h2>SMTP Settings</h2>

				<?php
				$setting = 'username';
				Get_Input::input(
					array(
						'setting'   => $setting,
						'wp_option' => self::OPTION,
						'value'     => $this->settings[ $setting ] ?? '',
						'label'     => $label = __( 'Username', 'error-monitor' ),
						'type'      => 'text',
						'classes'   => 'field-medium',
					)
				);

				$setting = 'password';
				Get_Input::input(
					array(
						'setting'   => $setting,
						'wp_option' => self::OPTION,
						'value'     => $this->settings[ $setting ] ?? '',
						'label'     => $label = __( 'Password', 'error-monitor' ),
						'type'      => 'password',
						'classes'   => 'field-medium',
					)
				);

				$setting = 'host';
				Get_Input::input(
					array(
						'setting'   => $setting,
						'wp_option' => self::OPTION,
						'value'     => $this->settings[ $setting ] ?? '',
						'label'     => $label = __( 'Host', 'error-monitor' ),
						'type'      => 'text',
						'classes'   => 'field-medium',
					)
				);

				$setting = 'port';
				Get_Input::select(
					array(
						'setting'   => $setting,
						'wp_option' => self::OPTION,
						'value'     => isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : '',
						'options'   => array(
							'25'   => '25',
							'465'  => '465',
							'587'  => '587',
							'2525' => '2525',
						),
						'label'     => __( 'Port' ),
						'classes'   => 'field-small',
					)
				);
				?>

				<hr>

				<h2>Message Sending</h2>

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
				$setting = 'from_email';
				Get_Input::input(
					array(
						'setting'   => $setting,
						'wp_option' => self::OPTION,
						'value'     => $this->settings[ $setting ] ?? '',
						'label'     => $label = __( 'Sent-from email address', 'error-monitor' ),
						'type'      => 'text',
						'classes'   => 'field-medium',
					)
				);

				$setting = 'to_email';
				Get_Input::input(
					array(
						'setting'   => $setting,
						'wp_option' => self::OPTION,
						'value'     => $this->settings[ $setting ] ?? '',
						'label'     => $label = __( 'Email to send notifications to', 'error-monitor' ),
						'type'      => 'text',
						'classes'   => 'field-medium',
					)
				);
				?>

				<h2><?php esc_html_e( 'Test Settings', 'error-monitor' ); ?></h2>
				<p><?php esc_html_e( 'Save your settings before testing connection and sending a test email.', 'error-monitor' ); ?></p>

				<div class="errorMonitor__testWrapper">

					<?php
					// SMTP Email test buttons.
					Get_Input::action_buttons(
						array(
							array(
								'action'               => 'test',
								'label'                => __( 'Test Connection', 'error-monitor' ),
								'primary_or_secondary' => 'secondary',
								'atttributes'          => array(
									'data-em-test' => 'smtp',
								),
								'flags'                => array(
									'disabled',
								),
							),
							array(
								'action'               => 'test',
								'label'                => __( 'Send Test Email', 'error-monitor' ),
								'primary_or_secondary' => 'secondary',
								'atttributes'          => array(
									'data-em-test' => 'email',
								),
								'flags'                => array(
									'disabled',
								),
							),
						),
					);
					?>

					<div id="errorMonitor__consoleOutput" class="errorMonitor__logOutput" style="display:none;"></div>

				</div>

			</form>
		</div>
		<?php
	}
}