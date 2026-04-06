<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Error Monitor - HTML template email.
 *
 * Fully email-safe version using table-based layout.
 */

// Variables passed from caller.
[ $email_header, $content, $layout ] = $variables;

$is_full_width = ( $layout ?? 'centered' ) === 'full-width';
$max_width     = $is_full_width ? '900px' : '600px';

$env = wp_get_environment_type();

$env_color = match ( $env ) {
	'production' => '#ef4444',
	'staging'    => '#f59e0b',
	default      => '#3b82f6',
};
?>
<!doctype html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo esc_html( $email_header ); ?></title>
<style media="all" type="text/css">
	@media only screen and (max-width: 640px) {
		#responsive-log-wrapper {
			padding: 6px !important;
		}
		#responsive-log-block {
			padding: 6px !important;
		}
		#responsive-header {
			padding: 16px 6px !important;
		}
	}
	@media all {
		.ExternalClass {
			width: 100%;
		}
		.ExternalClass,
		.ExternalClass p,
		.ExternalClass span,
		.ExternalClass font,
		.ExternalClass td,
		.ExternalClass div {
			line-height: 100%;
		}
		.apple-link a {
			color: inherit !important;
			font-family: inherit !important;
			font-size: inherit !important;
			font-weight: inherit !important;
			line-height: inherit !important;
			text-decoration: none !important;
		}
		#MessageViewBody a {
			color: inherit;
			text-decoration: none;
			font-size: inherit;
			font-family: inherit;
			font-weight: inherit;
			line-height: inherit;
		}
	}
</style>
</head>
<body style="
	margin:0;
	padding:0;
	background-color:#f4f5f6;
	font-family: Helvetica, Arial, sans-serif;
	-webkit-text-size-adjust:100%;
	-ms-text-size-adjust:100%;
">
<table id="OUTER_WRAPPER" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f4f5f6;">
	<tr>
		<td align="center" style="padding-top:24px;">

			<table id="CONTAINER" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:<?php echo esc_attr( $max_width ); ?>;">
				<tr>
					<td>

						<table id="CARD" width="100%" cellpadding="0" cellspacing="0" role="presentation"
							style="background:#ffffff; border:1px solid #eaebed; border-radius:16px;">

							<tr id="HEADER">
								<td 
									id="responsive-header"
									style="
									background:#111827;
									color:#ffffff;
									padding:16px 20px;
									font-size:14px;
									border-top-left-radius:16px;
									border-top-right-radius:16px;
								">

									<strong><?php echo esc_html( $email_header ); ?></strong>

									<span style="
										background:<?php echo esc_attr( $env_color ); ?>;
										color:#ffffff;
										padding:3px 8px;
										border-radius:6px;
										font-size:11px;
										margin-left:10px;
										display:inline-block;
									">
										<?php echo esc_html( strtoupper( $env ) ); ?>
									</span>

								</td>
							</tr>

							<tr id="CONTENT">
								<td id="responsive-log-wrapper" style="padding:24px; font-size:16px; line-height:1.4; color:#000000;">

									<?php echo $content; ?>

								</td>
							</tr>

						</table>

						<table id="FOOTER" width="100%" cellpadding="0" cellspacing="0" role="presentation">
							<tr>
								<td style="
									padding-top:24px;
									padding-bottom:24px;
									text-align:center;
									color:#9a9ea6;
									font-size:12px;
									font-family: Helvetica, Arial, sans-serif;
								">
									Powered by 
									<a href="https://webguyjeff.com" style="color:#9a9ea6; text-decoration:none;">
										Error Monitor
									</a>
								</td>
							</tr>
						</table>

					</td>
				</tr>
			</table>

		</td>
	</tr>
</table>

</body>
</html>