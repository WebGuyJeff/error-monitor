# Error Monitor

This plugin aims to build upon the reliable PHPMailer plugin and expand the static contact form into
customisable forms of any type built directly in the Gutenberg editor.

#### Goals:

- Build any form type.
- Preconfigured field blocks (name, email, etc) ready to drop-in.
- No input field configuration required.
- Customisable text block to create custom input fields.
- All input field blocks provide their own validation/sanitisation.

#### Support for classic plugin forms

This plugin was expanded from a classic forms plugin and as such the repo contains files speicific
to classic plugin functionality. These should be noted when classic support is removed:

- CSS and JS are still located in the traditinal directories in src/. The files are imported by the block JS and CSS files, but also used to generate assets which are enqueued using the traditional method in the init class. These files are also dependent on eachother through imports, so these must be picked apart and separated into their block files carefully. There may be need to maintain some common files where more than one block rely on a file.
- Classic classes have been moved into a classic-supports/ dir inside classes.

#### ToDo's

- field variation - move to 'transform to' on block menu bar thing.
- input name - enforce unique key within form.
- add input types: tickbox, calendar.
- enable questionaire type forms with inputs that change on input selections. E.g. a user chooses an option then different inputs are displayed for the required data capture.

#### Known bugs

- Form save as post not complete (don't use!).

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
