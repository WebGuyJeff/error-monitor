=== Error Monitor ===
Contributors: webguyjeff
Tags: error log, monitoring, php errors, alerts, smtp
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.0.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Monitor your WordPress/PHP error log, store structured issues in the database, and receive email alerts when new errors appear.

== Description ==

Error Monitor scans your configured error log on a schedule (or manually), classifies findings by severity, deduplicates repeated issues, and stores results in a custom WordPress table.

The plugin includes an admin interface for:

- scan scheduling and retention settings
- SMTP configuration and test actions
- automatic or manual log file selection
- grouped/raw log viewing in wp-admin
- optional toggles for `WP_DEBUG`, `WP_DEBUG_LOG`, and `WP_DEBUG_DISPLAY`

== Installation ==

1. Upload the plugin to `/wp-content/plugins/error-monitor`, or install it in your usual development workflow.
2. Install dependencies in the plugin directory:
`composer install`
3. Activate **WebGuyJeff: Error Monitor** from the Plugins screen.
4. Open the plugin settings page and configure:
   - monitoring frequency
   - retention period
   - SMTP account details
   - destination email
   - log file path (or use auto-discovery)

== Frequently Asked Questions ==

= Which log file does it scan by default? =

It attempts to use `WP_DEBUG_LOG` first, then falls back to the `php.ini` `error_log` path if available and readable.

= Does it scan the whole log every time? =

No. It tracks the last processed timestamp and only processes newer entries.

= What data is stored in the database? =

The plugin stores normalized and raw log messages, severity, first/last seen times, occurrence count, and recent timestamp history for each grouped issue.

= Can I run scans manually? =

Yes. Use the **Run Scan Now** action in the plugin admin page.

= What happens if SMTP is not configured? =

Scanning and log storage still work. Email notifications are skipped until SMTP settings are complete.

== Changelog ==

= 0.0.1 =
* Initial release of Error Monitor.
* Scheduled and manual log scanning.
* Severity classification and deduplicated log storage.
* SMTP notifications and admin log viewer.

== Upgrade Notice ==

= 0.0.1 =
Initial release.
