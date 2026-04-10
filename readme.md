# Error Monitor

Error Monitor is a WordPress plugin that watches your PHP error log, stores detected issues in the WordPress database, and sends notifications when new errors are found.

It provides a lightweight admin interface for configuring scan behavior, SMTP delivery, log source selection, and log review.

## Core Features

- Scheduled scans using WordPress Cron
- Manual scan trigger from wp-admin
- Automatic log file discovery (`WP_DEBUG_LOG` or `php.ini` error log)
- Configurable custom log file path
- Severity classification (`error`, `warning`, `notice`)
- Deduplication and occurrence tracking for repeated issues
- Email notifications for new findings and scan failures
- Built-in log viewer with grouped and raw output modes
- Optional wp-config debug constant toggles

## Plugin Workflow

1. A scheduled or manual scan runs.
2. The scanner reads only new log entries since the last processed timestamp.
3. Entries are parsed and normalized.
4. Logs are inserted or updated in a custom database table.
5. Retention cleanup removes old records.
6. If email settings are complete, notifications are sent.

## Requirements

- WordPress 6.0+
- PHP 8.0+
- MySQL
- Composer (dependency and tooling management)

## Installation

1. Place the plugin in:
   - `wp-content/plugins/error-monitor`
2. Install PHP dependencies:

```bash
composer install
```

3. Activate **WebGuyJeff: Error Monitor** in the WordPress Plugins screen.

## Configuration

Open the Error Monitor admin page and configure:

- **Monitor**
  - Enable/disable scheduled monitoring
  - Scan frequency in minutes
  - Log retention duration
  - Manual scan action
- **Email**
  - SMTP host, port, username, password
  - Sender and recipient addresses
  - SMTP connection test
  - Test email sending
- **Log File**
  - Auto-discover log file
  - Set a custom file path
  - View file source and readability status
  - Toggle `WP_DEBUG`, `WP_DEBUG_LOG`, and `WP_DEBUG_DISPLAY`
- **Logs**
  - Review stored log history
  - Switch between grouped and raw views

## Data Stored

### WordPress Option

- `error_monitor_settings`

### Database Table

- `{$wpdb->prefix}error_monitor_logs`

Stored fields include:

- Hash of normalized message
- Original message
- Normalized message
- Severity
- First seen timestamp
- Last seen timestamp
- Occurrence count
- Timestamp history (limited per grouped issue)

## Cron Behavior

- The scan event is scheduled on plugin activation.
- The schedule interval follows configured scan frequency.
- Schedule is updated when frequency changes.
- Scheduled scans are skipped when monitoring is disabled.
- The event is unscheduled on plugin deactivation.

## Development

Install dependencies:

```bash
composer install
```

Run coding standards checks:

```bash
composer lint
```

Auto-fix coding standards issues:

```bash
composer lint-fix
```

Run all tests:

```bash
composer test
```

Run unit tests only:

```bash
composer test:unit
```

Run integration tests only:

```bash
composer test:integration
```

## Test Environment Setup

1. Copy local test config:

```bash
cp tests/config-sample.php tests/config.php
```

2. Update `tests/config.php` for your environment.

3. Install the WordPress test suite:

```bash
bin/install-wp-tests.sh
```

## License

GPL-2.0-or-later.
# Error Monitor

`Error Monitor` is a WordPress plugin that scans your PHP error log, stores structured results in the WordPress database, and sends email alerts when new errors are detected.

It is designed for site owners and developers who want lightweight, in-dashboard visibility of runtime issues without manually tailing server logs.

## Features

- Scheduled log scanning via WordPress Cron
- Manual on-demand scan from wp-admin
- Auto-discovery of log file path (`WP_DEBUG_LOG` or `php.ini` error log)
- Structured log storage in a custom database table
- Deduplication by normalized message hash
- Severity tagging (`error`, `warning`, `notice`)
- Email notifications through SMTP
- Log viewer in wp-admin (grouped and raw views)
- Optional debug constant toggles for `WP_DEBUG`, `WP_DEBUG_LOG`, and `WP_DEBUG_DISPLAY`

## How It Works

1. A scan runs on a configured interval (or manually).
2. The scanner reads the log file from the end and only processes entries newer than the last seen timestamp.
3. Entries are parsed, normalized, and classified by severity.
4. Logs are inserted/updated in the `{$wpdb->prefix}error_monitor_logs` table.
5. If SMTP is configured, a notification email is sent when new logs are found.

## Requirements

- WordPress (plugin context)
- PHP 7.4 or higher
- Composer (for dependencies and developer tooling)
- MySQL

## Installation

1. Place this plugin in:
   - `wp-content/plugins/error-monitor`
2. Install dependencies:

```bash
composer install
```

3. Activate **WebGuyJeff: Error Monitor** from the WordPress Plugins screen.

## Configuration

After activation, open the plugin settings in wp-admin and configure:

- **Monitor**
  - Enable/disable scheduled monitoring
  - Scan frequency in minutes
  - Log retention period
  - Manual scan trigger
- **Email**
  - SMTP host, port, username, password
  - From and destination email addresses
  - Connection and email test actions
- **Log File**
  - Auto-discover or manually set log file path
  - View file status (exists/readable/source)
  - Toggle WP debug constants (if `wp-config.php` is writable)
- **Logs**
  - Browse stored logs in grouped or raw view

## Data Storage

The plugin creates and maintains:

- Custom table: `{$wpdb->prefix}error_monitor_logs`
- Option: `error_monitor_settings`

Stored log records include:

- Raw message
- Normalized message
- Severity
- First/last seen timestamps
- Occurrence count
- Recent timestamp history (up to 100 per grouped log)

Old records are cleaned up according to the configured retention period.

## Cron Behavior

- The plugin schedules `error_monitor_scan_logs` on activation.
- Schedule frequency is dynamically derived from plugin settings.
- If monitoring is disabled, scheduled scans are skipped.
- Cron is unscheduled on plugin deactivation.

## Development

### Install Dependencies

```bash
composer install
```

### Lint

```bash
composer lint
```

### Auto-fix Coding Standards

```bash
composer lint-fix
```

### Run Tests

```bash
# All tests
composer test

# Unit tests only
composer test:unit

# Integration tests only
composer test:integration
```

## Test Environment Setup

1. Copy local test config:

```bash
cp tests/config-sample.php tests/config.php
```

2. Update `tests/config.php` if needed.

3. Install WordPress test suite:

```bash
bin/install-wp-tests.sh
```

## Notes

- This repository still contains some outdated metadata strings in non-runtime files (for example, legacy descriptions/namespaces in `composer.json`). Runtime plugin behavior reflects the error-monitoring functionality documented here.

## License

GPL-2.0-or-later (plugin header includes GPL references).

---

## Testing

This plugin uses PHPUnit for automated testing with both unit tests and WordPress integration tests.

## Prerequisites

- PHP 7.4 or higher
- Composer
- MySQL
- Subversion (SVN)

## Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Tests

Copy the sample configuration:

```bash
cp tests/config-sample.php tests/config.php
```

Edit `tests/config.php` if you need to customize database settings or file paths.

### 3. Install WordPress Test Suite

Run the installation script (you'll be prompted for your MySQL password):

```bash
bin/install-wp-tests.sh
```

The script will:

- Download WordPress core
- Download the WordPress test suite
- Create a test database
- Configure the test environment

**Note:** Your MySQL password is never stored in files - it's only used during installation.

## Running Tests

```bash
# Run all tests
composer test

# Run only unit tests
composer test:unit

# Run only integration tests
composer test:integration
```

## Test Structure

```
tests/
├── bootstrap.php          # Test suite bootstrap
├── config.php            # Local configuration (not committed)
├── config-sample.php     # Configuration template
├── unit/                 # Unit tests (isolated, no WordPress dependencies)
│   └── test-*.php
└── integration/          # Integration tests (with WordPress)
    └── test-*.php
```

## Writing Tests

### Unit Tests

Test individual functions in isolation:

```php
<?php
namespace MyPlugin\Tests\Unit;

use WP_UnitTestCase;

class MyTest extends WP_UnitTestCase {
    public function test_something() {
        $result = my_function( 'input' );
        $this->assertEquals( 'expected', $result );
    }
}
```

### Integration Tests

Test functionality with WordPress:

```php
<?php
namespace MyPlugin\Tests\Integration;

use WP_UnitTestCase;

class MyIntegrationTest extends WP_UnitTestCase {
    public function test_creates_post() {
        $post_id = $this->factory->post->create([
            'post_title' => 'Test'
        ]);

        $this->assertGreaterThan( 0, $post_id );
    }
}
```

## CI/CD

For continuous integration, set the MySQL password via environment variable:

```bash
export WP_TEST_DB_PASSWORD="your_password"
bin/install-wp-tests.sh
composer test
```

## Troubleshooting

**Tests not found:** Ensure WordPress test suite is installed with `bin/install-wp-tests.sh`

**Database errors:** Check your MySQL credentials in `tests/config.php`

**Permission errors:** Ensure `/tmp` directory is writable or change paths in `tests/config.php`
