<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Error Monitor - HTML template email.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2026, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */

// Variables passed from caller.
[ $notification_name, $site_domain, $fields, $email_header ] = $variables;

$fields_html = "\n";
foreach ( $fields as $name => $data ) {
	$uc_name      = ucfirst( str_replace( '-', ' ', $name ) );
	$fields_html .= <<<HTML
		<tr>
			<td class="field-cell" style="font-family: Helvetica, sans-serif; font-size: 16px; vertical-align: top; padding-top: 3px; padding-bottom: 3px;" valign="top">
				<span class="field-title" style="color: #b8b8b8;"><b>{$uc_name}</b></span>
				<p style="font-family: Helvetica, sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 16px;">{$data['value']}</p>
			</td>
		</tr>
	HTML;
	$fields_html .= "\n";
}

?>

<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo esc_html( $email_header ); ?></title>
<style media="all" type="text/css">
	@media all {
		.btn-primary table td:hover {
		background-color: #0acf83 !important;
		}

		.btn-primary a:hover {
			background-color: #0acf83 !important;
			border-color: #0acf83 !important;
		}
	}
	@media only screen and (max-width: 640px) {
		.main p,
		.main td,
		.main span {
			font-size: 16px !important;
		}

		.wrapper {
			padding: 8px !important;
		}

		.content {
			padding: 0 !important;
		}

		.container {
			padding: 0 !important;
			padding-top: 8px !important;
			width: 100% !important;
		}

		.main {
			border-left-width: 0 !important;
			border-radius: 0 !important;
			border-right-width: 0 !important;
		}

		.btn table {
			max-width: 100% !important;
			width: 100% !important;
		}

		.btn a {
			font-size: 16px !important;
			max-width: 100% !important;
			width: 100% !important;
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

<body style="font-family: Helvetica, sans-serif; -webkit-font-smoothing: antialiased; font-size: 16px; line-height: 1.3; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #f4f5f6; margin: 0; padding: 0;">
	<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f4f5f6; width: 100%;" width="100%" bgcolor="#f4f5f6">
		<tbody>
			<tr>
				<td style="font-family: Helvetica, sans-serif; font-size: 16px; vertical-align: top;" valign="top">&nbsp;</td>
				<td class="container" style="font-family: Helvetica, sans-serif; font-size: 16px; vertical-align: top; max-width: 600px; padding: 0; padding-top: 24px; width: 600px; margin: 0 auto;" width="600" valign="top">
					<div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 600px; padding: 0;">

						<!-- START CENTERED WHITE CONTAINER -->
						<span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;"><?php __( 'New website form submission.', 'error-monitor' ); ?></span>
						<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #ffffff; border: 1px solid #eaebed; border-radius: 16px; width: 100%;" width="100%">

							<!-- START MAIN CONTENT AREA -->
							<tbody>
								<tr>
									<td class="wrapper" style="font-family: Helvetica, sans-serif; font-size: 16px; vertical-align: top; box-sizing: border-box; padding: 24px;" valign="top">
										<p style="font-family: Helvetica, sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 16px;"><?php __( 'Hi,', 'error-monitor' ); ?></p>
										<p style="font-family: Helvetica, sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 16px;">
											<?php echo wp_kses( $email_header, wp_kses_allowed_html( 'post' ) ); ?>
										</p>
										<hr>
										<br>

										<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
											<tbody>
												<tr>
													<td align="center" style="font-family: Helvetica, sans-serif; font-size: 16px; vertical-align: top;" valign="top">
														<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
															<tbody>
																<?php echo wp_kses( $fields_html, wp_kses_allowed_html( 'post' ) ); ?>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
										
										<br>

									</td>
								</tr>
								<!-- END MAIN CONTENT AREA -->

							</tbody>
						</table>
						<!-- END CENTERED WHITE CONTAINER -->

						<!-- START FOOTER -->
						<div class="footer" style="clear: both; padding-top: 24px; text-align: center; width: 100%;">
							<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
								<tbody>
									<tr>
										<td class="content-block powered-by" style="font-family: Helvetica, sans-serif; vertical-align: top; color: #9a9ea6; font-size: 16px; text-align: center;" valign="top" align="center">
											Powered by <a href="https://webguyjeff.com" style="color: #9a9ea6; font-size: 16px; text-align: center; text-decoration: none;">Error Monitor</a>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<!-- END FOOTER -->
						
						<br>

					</div>
				</td>
				<td style="font-family: Helvetica, sans-serif; font-size: 16px; vertical-align: top;" valign="top">&nbsp;</td>
			</tr>
		</tbody>
	</table>

</body>

</html>