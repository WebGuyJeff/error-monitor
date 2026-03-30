<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Email Log Renderer.
 *
 * Generates email-safe HTML using tables + inline styles.
 */
class Log_Renderer_Email_Safe {

	public static function render( array $entries ): string {

		$groups = Log_View_Model::grouped( $entries );

		ob_start();
		?>

		<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:16px;">

			<?php foreach ( $groups as $group ) : ?>

				<tr>
					<td style="
						color:#9ca3af;
						font-size:11px;
						padding-top:14px;
						padding-bottom:6px;
						text-transform:uppercase;
						letter-spacing:0.5px;
					">
						<?php echo esc_html( strtoupper( $group['label'] ) ); ?>
					</td>
				</tr>

				<?php foreach ( $group['logs'] as $log ) : ?>

					<?php
					$color = match ( $log['severity'] ) {
						'error'   => '#ef4444',
						'warning' => '#f59e0b',
						'notice'  => '#3b82f6',
						default   => 'rgba(255,255,255,0.15)',
					};
					?>

					<tr>
						<td style="padding-bottom:6px;">

							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>

									<td width="4" style="background:<?php echo esc_attr( $color ); ?>;"></td>

									<td style="padding-left:10px; font-family: monospace; font-size:13px; line-height:1.5;">

										<table width="100%">
											<tr>

												<td>
													<?php echo esc_html( trim( $log['message'] ) ); ?>
												</td>

												<?php if ( (int) $log['count'] > 1 ) : ?>
													<td align="right" style="color:#60a5fa; font-size:12px; white-space:nowrap;">
														&times;<?php echo (int) $log['count']; ?>
													</td>
												<?php endif; ?>

											</tr>
										</table>

									</td>

								</tr>
							</table>

						</td>
					</tr>

				<?php endforeach; ?>

			<?php endforeach; ?>

		</table>

		<?php

		return trim( ob_get_clean() );
	}
}
