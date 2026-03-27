<?php

namespace WebGuyJeff\Error_Monitor\Tests\Integration;

use WP_UnitTestCase;
use WebGuyJeff\Error_Monitor\Settings;


/**
 * Tests covered:
 *  - Happy path testing: Valid SMTP and local mail server settings
 *  - Missing settings: When options don't exist in database
 *  - Invalid emails: Various invalid email formats for both from_email and to_email
 *  - Valid emails: Multiple valid email formats
 *  - Invalid ports: Testing with non-standard ports
 *  - Valid ports: All four valid port numbers (25, 465, 587, 2525)
 *  - Invalid host: Non-resolvable hostname
 *  - Type validation: Non-string username, empty password
 *  - Boolean validation: Both true/false values for auth and use_local_mail_server
 *  - Edge cases: Empty strings, missing fields
 */
class GetSettingsTest extends WP_UnitTestCase {
    
    /**
     * Clean up after each test
     */
    public function tearDown(): void {
        delete_option( 'error_monitor_settings' );
        parent::tearDown();
    }
    
    /**
     * Test SMTP settings with all valid values
     */
    public function test_smtp_returns_settings_when_all_valid() {
        // Arrange - set up valid SMTP settings
        $valid_settings = [
            'username' => 'testuser',
            'password' => 'testpass123',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'auth' => true,
            'use_local_mail_server' => false,
            'from_email' => 'sender@example.com',
            'to_email' => 'recipient@example.com',
        ];
        update_option( 'error_monitor_settings', $valid_settings );
        
        // Act
        $result = Settings::get();
        
        // Assert
        $this->assertIsArray( $result );
        $this->assertEquals( 'testuser', $result['username'] );
        $this->assertEquals( 'testpass123', $result['password'] );
        $this->assertEquals( 'smtp.gmail.com', $result['host'] );
        $this->assertEquals( 587, $result['port'] );
        $this->assertTrue( $result['auth'] );
        $this->assertFalse( $result['use_local_mail_server'] );
        $this->assertEquals( 'sender@example.com', $result['from_email'] );
        $this->assertEquals( 'recipient@example.com', $result['to_email'] );
    }
    
    /**
     * Test SMTP settings returns false when settings don't exist
     */
    public function test_smtp_returns_false_when_no_settings_exist() {
        // Don't set any options
        
        $result = Settings::get();
        
        $this->assertFalse( $result );
    }
    
    /**
     * Test SMTP settings returns false with invalid email
     */
    public function test_smtp_returns_false_with_invalid_from_email() {
        $invalid_settings = [
            'username' => 'testuser',
            'password' => 'testpass123',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'auth' => true,
            'use_local_mail_server' => false,
            'from_email' => 'not-an-email',
            'to_email' => 'recipient@example.com',
        ];
        update_option( 'error_monitor_settings', $invalid_settings );
        
        $result = Settings::get();
        
        $this->assertFalse( $result );
    }
    
    /**
     * Test SMTP settings returns false with invalid to_email
     */
    public function test_smtp_returns_false_with_invalid_to_email() {
        $invalid_settings = [
            'username' => 'testuser',
            'password' => 'testpass123',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'auth' => true,
            'use_local_mail_server' => false,
            'from_email' => 'sender@example.com',
            'to_email' => 'invalid@email@domain.com',
        ];
        update_option( 'error_monitor_settings', $invalid_settings );
        
        $result = Settings::get();
        
        $this->assertFalse( $result );
    }
    
    /**
     * Test SMTP settings returns false with invalid port
     */
    public function test_smtp_returns_false_with_invalid_port() {
        $invalid_settings = [
            'username' => 'testuser',
            'password' => 'testpass123',
            'host' => 'smtp.gmail.com',
            'port' => 999, // Invalid port
            'auth' => true,
            'use_local_mail_server' => false,
            'from_email' => 'sender@example.com',
            'to_email' => 'recipient@example.com',
        ];
        update_option( 'error_monitor_settings', $invalid_settings );
        
        $result = Settings::get();
        
        $this->assertFalse( $result );
    }
    
    /**
     * Test SMTP settings with all valid port numbers
     */
    public function test_smtp_accepts_all_valid_ports() {
        $valid_ports = [ 25, 465, 587, 2525 ];
        
        foreach ( $valid_ports as $port ) {
            $settings = [
                'username' => 'testuser',
                'password' => 'testpass123',
                'host' => 'smtp.gmail.com',
                'port' => $port,
                'auth' => true,
                'use_local_mail_server' => false,
                'from_email' => 'sender@example.com',
                'to_email' => 'recipient@example.com',
            ];
            update_option( 'error_monitor_settings', $settings );
            
            $result = Settings::get();
            
            $this->assertIsArray( $result, "Port $port should be valid" );
            $this->assertEquals( $port, $result['port'] );
        }
    }
    
    /**
     * Test SMTP settings returns false with invalid host
     */
    public function test_smtp_returns_false_with_invalid_host() {
        $invalid_settings = [
            'username' => 'testuser',
            'password' => 'testpass123',
            'host' => 'this-is-not-a-valid-host-12345.invalid',
            'port' => 587,
            'auth' => true,
            'use_local_mail_server' => false,
            'from_email' => 'sender@example.com',
            'to_email' => 'recipient@example.com',
        ];
        update_option( 'error_monitor_settings', $invalid_settings );
        
        $result = Settings::get();
        
        $this->assertFalse( $result );
    }
    
    /**
     * Test SMTP settings returns false when username is not a string
     */
    public function test_smtp_returns_false_with_non_string_username() {
        $invalid_settings = [
            'username' => 12345, // Not a string
            'password' => 'testpass123',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'auth' => true,
            'use_local_mail_server' => false,
            'from_email' => 'sender@example.com',
            'to_email' => 'recipient@example.com',
        ];
        update_option( 'error_monitor_settings', $invalid_settings );
        
        $result = Settings::get();
        
        $this->assertFalse( $result );
    }
    
    /**
     * Test SMTP settings returns false when password is missing
     */
    public function test_smtp_returns_false_when_password_missing() {
        $invalid_settings = [
            'username' => 'testuser',
            // password missing
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'auth' => true,
            'use_local_mail_server' => false,
            'from_email' => 'sender@example.com',
            'to_email' => 'recipient@example.com',
        ];
        update_option( 'error_monitor_settings', $invalid_settings );
        
        $result = Settings::get();
        
        $this->assertFalse( $result );
    }
    
    /**
     * Test auth setting accepts boolean values
     */
    public function test_smtp_accepts_boolean_auth_values() {
        // Test with true
        $settings = [
            'username' => 'testuser',
            'password' => 'testpass123',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'auth' => true,
            'use_local_mail_server' => false,
            'from_email' => 'sender@example.com',
            'to_email' => 'recipient@example.com',
        ];
        update_option( 'error_monitor_settings', $settings );
        
        $result = Settings::get();
        $this->assertIsArray( $result );
        $this->assertTrue( $result['auth'] );
        
        // Test with false
        $settings['auth'] = false;
        update_option( 'error_monitor_settings', $settings );
        
        $result = Settings::get();
        $this->assertIsArray( $result );
        $this->assertFalse( $result['auth'] );
    }
    
    /**
     * Test use_local_mail_server setting accepts boolean values
     */
    public function test_smtp_accepts_boolean_use_local_mail_server_values() {
        // Test with true
        $settings = [
            'username' => 'testuser',
            'password' => 'testpass123',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'auth' => true,
            'use_local_mail_server' => true,
            'from_email' => 'sender@example.com',
            'to_email' => 'recipient@example.com',
        ];
        update_option( 'error_monitor_settings', $settings );
        
        $result = Settings::get();
        $this->assertIsArray( $result );
        $this->assertTrue( $result['use_local_mail_server'] );
        
        // Test with false
        $settings['use_local_mail_server'] = false;
        update_option( 'error_monitor_settings', $settings );
        
        $result = Settings::get();
        $this->assertIsArray( $result );
        $this->assertFalse( $result['use_local_mail_server'] );
    }
    
    /**
     * Test empty string password is invalid
     */
    public function test_smtp_returns_false_with_empty_password() {
        $invalid_settings = [
            'username' => 'testuser',
            'password' => '',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'auth' => true,
            'use_local_mail_server' => false,
            'from_email' => 'sender@example.com',
            'to_email' => 'recipient@example.com',
        ];
        update_option( 'error_monitor_settings', $invalid_settings );
        
        $result = Settings::get();
        
        $this->assertFalse( $result );
    }
    
    /**
     * Test various invalid email formats
     */
    public function test_smtp_rejects_various_invalid_email_formats() {
        $invalid_emails = [
            'plaintext',
            '@example.com',
            'user@',
            'user @example.com',
            'user@example .com',
            'user@@example.com',
            '',
        ];
        
        foreach ( $invalid_emails as $invalid_email ) {
            $settings = [
                'username' => 'testuser',
                'password' => 'testpass123',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'auth' => true,
                'use_local_mail_server' => false,
                'from_email' => $invalid_email,
                'to_email' => 'recipient@example.com',
            ];
            update_option( 'error_monitor_settings', $settings );
            
            $result = Settings::get();
            
            $this->assertFalse( $result, "Email '$invalid_email' should be invalid" );
        }
    }
    
    /**
     * Test various valid email formats are accepted
     */
    public function test_smtp_accepts_various_valid_email_formats() {
        $valid_emails = [
            'user@example.com',
            'user.name@example.com',
            'user+tag@example.co.uk',
            'user_name@example-domain.com',
            '123@example.com',
        ];
        
        foreach ( $valid_emails as $valid_email ) {
            $settings = [
                'username' => 'testuser',
                'password' => 'testpass123',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'auth' => true,
                'use_local_mail_server' => false,
                'from_email' => $valid_email,
                'to_email' => 'recipient@example.com',
            ];
            update_option( 'error_monitor_settings', $settings );
            
            $result = Settings::get();
            
            $this->assertIsArray( $result, "Email '$valid_email' should be valid" );
        }
    }
}
