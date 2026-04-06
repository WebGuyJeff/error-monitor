<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Settings Tab Log File.
 */
class Settings_Page_Log_File {

	public const OPTION = 'error_monitor_settings';

	private $settings;


	public function __construct() {
		$this->settings = Settings::get();
	}


	/**
	 * Output page.
	 */
	public function output() {

		$discovery = new Log_File_Discovery();
		$status    = $discovery->get_status();

		?>
		<div class="adminPage_container">

			<form>

				<h2>Log File</h2>

				<table class="widefat striped">
					<thead>
						<tr>
							<th scope="col" colspan="2">Status</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>Path</th>
							<td><?php echo esc_html( $status['path'] ?? 'Not set' ); ?></td>
						</tr>
						<tr>
							<th>Source</th>
							<td><?php echo esc_html( $status['source'] ); ?></td>
						</tr>
						<tr>
							<th>Exists</th>
							<td><?php echo $status['exists'] ? 'Yes' : 'No'; ?></td>
						</tr>
						<tr>
							<th>Readable</th>
							<td><?php echo $status['readable'] ? 'Yes' : 'No'; ?></td>
						</tr>
					</tbody>
				</table>

				<?php
				$setting = 'log_file_path';
				Get_Input::input(
					array(
						'setting'     => $setting,
						'wp_option'   => self::OPTION,
						'value'       => $this->settings[ $setting ] ?? '',
						'label'       => $label = __( 'Log File Path', 'error-monitor' ),
						'description' => $description = __( 'Configure the path to the error log file.', 'error-monitor' ),
						'type'        => 'text',
						'classes'     => 'field-large',
					)
				);
				?>

				<?php
				Get_Input::action_buttons(
					array(
						array(
							'action'               => 'discover_log',
							'label'                => __( 'Auto Discover Log File', 'error-monitor' ),
							'primary_or_secondary' => 'secondary',
							'atttributes'          => array(
								'data-em-test' => 'smtp',
							),
						),
					)
				);
				?>

				<hr>

				<h2>Debug Configuration</h2>

				<label>
					<input data-em-action="apply_debug" data-em-debug="wp_debug" type="checkbox" name="wp_debug_enabled" value="1" <?php checked( defined( 'WP_DEBUG' ) && WP_DEBUG ); ?>>
					WP_DEBUG
				</label><br>

				<label>
					<input data-em-action="apply_debug" data-em-debug="wp_debug_log" type="checkbox" name="wp_debug_log_enabled" value="1" <?php checked( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ); ?>>
					WP_DEBUG_LOG
				</label><br>

				<label>
					<input data-em-action="apply_debug" data-em-debug="wp_debug_display" type="checkbox" name="wp_debug_display_enabled" value="1" <?php checked( defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ); ?>>
					WP_DEBUG_DISPLAY
				</label><br><br>

			</form>

		</div>
		<?php
	}
}
