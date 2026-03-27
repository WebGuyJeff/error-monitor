<?php
/**
 * Test configuration sample
 * Copy this to tests/config.php and update with your local settings
 */

return [
    // WordPress test suite directory
    'WP_TESTS_DIR' => getenv('WP_TESTS_DIR') ?: '/tmp/wordpress-tests-lib',
    
    // WordPress core directory
    'WP_CORE_DIR' => getenv('WP_CORE_DIR') ?: '/tmp/wordpress',
    
    // Plugin configuration
    'PLUGIN_FILE' => 'my-plugin.php',
    'PLUGIN_SLUG' => 'my-plugin',
    
    // Database configuration (for tests)
    // Note: Password is never stored - you'll be prompted during setup
    'DB_NAME' => getenv('WP_TEST_DB_NAME') ?: 'wordpress_test',
    'DB_USER' => getenv('WP_TEST_DB_USER') ?: 'root',
    'DB_HOST' => getenv('WP_TEST_DB_HOST') ?: 'localhost',
];