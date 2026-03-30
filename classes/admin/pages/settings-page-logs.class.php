<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Settings Tab: Logs Viewer.
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

				<!-- VIEW TOGGLE -->
				<div class="adminButtonRow">

					<a
						href="?page=<?php echo esc_attr( Admin_Settings::SETTINGSLUG ); ?>&tab=tab-3&view=grouped"
						class="button <?php echo $view === 'grouped' ? 'button-primary' : 'button-secondary'; ?>">
						Grouped View
					</a>

					<a
						href="?page=<?php echo esc_attr( Admin_Settings::SETTINGSLUG ); ?>&tab=tab-3&view=raw"
						class="button <?php echo $view === 'raw' ? 'button-primary' : 'button-secondary'; ?>">
						Raw View
					</a>

				</div>

				<?php if ( empty( $entries ) ) : ?>

					<div class="notice notice-info inline">
						<p><?php esc_html_e( 'No logs found.', 'error-monitor' ); ?></p>
					</div>

				<?php else : ?>

					<?php if ( 'grouped' === $view ) : ?>

						<?php $this->render_grouped( $entries ); ?>

					<?php else : ?>

						<?php $this->render_raw( $entries ); ?>

					<?php endif; ?>

				<?php endif; ?>

			</div>
		</div>

		<?php
	}

	/**
	 * Grouped view (same as email).
	 */
	private function render_grouped( array $entries ): void {

		$grouped = Log_Formatter::grouped( $entries );

		?>

		<div class="errorMonitor__logOutput">
			<?php echo Log_Renderer_Email_Safe::render( $entries ); ?>
		</div>

		<p style="font-size:12px;color:#777;">
			Only the last 100 occurrences per log are stored.
		</p>

		<?php
	}

	/**
	 * Raw chronological view.
	 */
	private function render_raw( array $entries ): void {

		?>

		<div class="errorMonitor__logOutput">
			<pre><?php echo esc_html( implode( "\n", array_column( $entries, 'raw' ) ) ); ?></pre>
		</div>

		<p style="font-size:12px;color:#777;">
			Showing chronological log entries (expanded from stored timestamps).
		</p>

		<?php
	}
}