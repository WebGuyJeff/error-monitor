<?php
use WebGuyJeff\Error_Monitor\Log_Renderer_Email_Safe;
use WebGuyJeff\Error_Monitor\Admin_Page;

[ $data ] = $variables;

$count     = intval( $data['count'] ?? 0 );
$total     = intval( $data['total'] ?? 0 );
$unique    = intval( $data['unique'] ?? 0 );
$entries   = is_array( $data['entries'] ?? null ) ? $data['entries'] : array();
$first     = $data['first'] ?? null;
$last      = $data['last'] ?? null;
$duration  = ( $first && $last ) ? ( $last - $first ) : 0;
$admin_url = Admin_Page::url( 'logs', 'url' );
?>

<table id="WRAPPER_TABLE" width="100%" cellpadding="0" cellspacing="0" role="presentation">

	<tr id="HEADER_TEXT">
		<td style="font-family: Helvetica, sans-serif; font-size:16px; padding-bottom:12px;">
			<strong>
				<?php echo $count . ' ' . ( $count === 1 ? 'error' : 'errors' ); ?> detected
			</strong>
		</td>
	</tr>

	<tr id="DIVIDER">
		<td style="padding-bottom:14px;">
			<hr style="border:none; border-top:1px solid #e5e7eb;">
		</td>
	</tr>

	<tr id="META">
		<td style="font-size:13px; color:#6b7280; padding-bottom:14px; font-family: Helvetica, sans-serif;">

			<strong><?php echo $unique; ?></strong> unique issue<?php echo $unique === 1 ? '' : 's'; ?>
			&nbsp;•&nbsp;
			<strong><?php echo $total; ?></strong> total events

			<?php if ( $duration > 0 ) : ?>
				&nbsp;•&nbsp;
				<?php echo gmdate( 'H:i:s', $duration ); ?> timespan
			<?php endif; ?>

		</td>
	</tr>

	<tr id="LOG_BLOCK">
		<td style="padding-top:10px;">
			<table id="responsive-log-block" width="100%" cellpadding="0" cellspacing="0" role="presentation"
				style="
					border-radius:8px;
					background:#0f172a;
					padding:16px;
					line-height:1.5;
					color:#e5e7eb;
					font-family: monospace;
					font-size:13px;
				"
			>
				<tr>
					<td>
						<?php echo Log_Renderer_Email_Safe::render( $entries ); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr id="CTA_BUTTON">
		<td style="padding-top:18px;">
			<table cellpadding="0" cellspacing="0" role="presentation">
				<tr>
					<td style="background:#2563eb; border-radius:6px;">
						<a
							href="<?php echo esc_url( $admin_url ); ?>"
							style="
								display:inline-block;
								padding:10px 14px;
								text-decoration:none;
								color:#ffffff;
								font-family: Helvetica, sans-serif;
								font-size:13px;
						">
							View full logs →
						</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>
