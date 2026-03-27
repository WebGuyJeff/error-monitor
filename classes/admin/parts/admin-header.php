<?php

/**
 * Admin header template.
 */

[
	$plugin_name,
	$email_configured,
	$cron_scheduled,
] = $variables;

?>
<header class="adminHeader">
	<?php // Form save notices will always be injected after .wp-header-end.
		echo '<hr style="display: none;" class="wp-header-end">';
		settings_errors();
	?>
	<div class="adminTitle">
		<span class="dashicons-webguyjeff-logo" style="font-size: 2em; margin-right: 0.2em;"></span>
		<div>
			<h1><?php echo $plugin_name; ?></h1>
			<p>Get notified about new errors on your WordPress site</p>
		</div>
	</div>
	<hr class="adminHeaderDivider">
	<div class="pluginStatus">
		<strong>Status</strong>
		<span><?php $email_configured ? _e( '✅ Email configured', 'error_monitor' ) : _e( '❌ Email not configured', 'error_monitor' ); ?></span>
		<span><?php $cron_scheduled ? _e( '✅ Scan scheduled', 'error_monitor' ) : _e( '❌ Scan not scheduled', 'error_monitor' ); ?></span>
	</div>
</header>
