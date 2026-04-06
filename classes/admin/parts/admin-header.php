<?php

/**
 * Admin header template.
 */

use WebGuyJeff\Error_Monitor\Timestamp;

[
	$plugin_name,
	$email_configured,
	$cron_scheduled,
	$last_scan_time,
	$last_log_timestamp,
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
		<span><?php $last_scan_time ? _e( '🔍 Last scan: ' . Timestamp::unix_to_readable( $last_scan_time ), 'error_monitor' ) : _e( '🔍 Last scan: Never', 'error_monitor' ); ?></span>
		<span><?php $last_log_timestamp ? _e( '📝 Last log: ' . Timestamp::unix_to_readable( $last_log_timestamp ), 'error_monitor' ) : _e( '📃 Last log: Never', 'error_monitor' ); ?></span>
		<div>
			<span><?php $email_configured ? _e( '✅ Email configured', 'error_monitor' ) : _e( '❌ Email not configured', 'error_monitor' ); ?></span>
			<span><?php $cron_scheduled ? _e( '✅ Scan scheduled', 'error_monitor' ) : _e( '❌ Scan not scheduled', 'error_monitor' ); ?></span>
		</div>
	</div>
</header>
