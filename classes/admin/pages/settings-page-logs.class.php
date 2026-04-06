<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Settings Tab Logs.
 *
 * @package error-monitor
 */
class Settings_Page_Logs {

	public function output() {

		$repo    = new Log_Repository();
		$entries = $repo->get_recent_logs( 300 );

		$total = count( $entries );

		$view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : 'grouped';

		?>

		<div class="adminPage_container fullWidth">
			<h2><?php esc_html_e( 'Log Viewer', 'error-monitor' ); ?></h2>
			<p><?php esc_html_e( 'Browse stored logs from the database.', 'error-monitor' ); ?></p>

			<hr>

			<div class="errorMonitor__logViewer">

				<div class="errorMonitor__logMeta">
					<strong><?php esc_html_e( 'Logs Loaded:', 'error-monitor' ); ?></strong>
					<?php echo esc_html( (string) $total ); ?>
				</div>

				<?php
				// Log view buttons.
				Get_Input::action_buttons(
					array(
						array(
							'action'               => 'fetch_logs',
							'label'                => __( 'Grouped View', 'error-monitor' ),
							'primary_or_secondary' => 'secondary',
							'atttributes' => array(
								'data-em-view' => 'grouped',
							),
						),
						array(
							'action'               => 'fetch_logs',
							'label'                => __( 'Raw View', 'error-monitor' ),
							'primary_or_secondary' => 'secondary',
							'atttributes' => array(
								'data-em-view' => 'raw',
							),
						),
					),
				);
				?>

				<?php if ( empty( $entries ) ) : ?>

					<div class="notice notice-info inline">
						<p><?php esc_html_e( 'No logs found.', 'error-monitor' ); ?></p>
					</div>

				<?php else :

					$grouped = Log_Formatter::grouped( $entries );
					?>

					<div class="errorMonitor__logOutput" data-em-log-output>
						<?php echo Log_Renderer_Email_Safe::render( $entries ); ?>
					</div>

					<p style="font-size:12px;color:#777;">
						Only the last 100 occurrences per log are stored.
					</p>

				<?php endif; ?>

			</div>
		</div>

		<?php
	}
}