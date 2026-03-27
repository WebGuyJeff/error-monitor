<?php

namespace WebGuyJeff\Error_Monitor\Tests\Unit;

use WP_UnitTestCase;
use WebGuyJeff\Error_Monitor\HTTP_Response;

/**
 * Test suite for HTTP_Response class
 * 
 * Tests covered:
 * - test_send_json_with_success_status_code_sets_ok_to_true
 * - test_send_json_with_error_status_code_sets_ok_to_false
 * - test_send_json_with_redirect_status_code_sets_ok_to_false
 * - test_send_json_converts_string_message_to_array
 * - test_send_json_keeps_array_message_as_array
 * - test_send_json_includes_data_in_response
 * - test_send_json_with_empty_data_array
 * - test_send_json_with_multiple_messages
 * - test_send_json_handles_non_array_status_with_default_500_error
 * - test_send_json_response_is_valid_json
 * - test_send_json_clears_output_buffer
 * - test_send_json_response_structure_is_correct
 * - test_send_json_with_various_2xx_status_codes
 * - test_send_json_with_various_4xx_status_codes
 * - test_send_json_with_various_5xx_status_codes
 * - test_send_json_handles_complex_data_structures
 */
class HTTPResponseTest extends WP_UnitTestCase {
    
    /**
     * Helper method to capture output from send_json
     */
    private function capture_json_output( $status, $data = array() ) {
        ob_start();
        ob_start(); // Double buffer to handle ob_clean() in the method
        HTTP_Response::send_json( $status, $data );
        $output = ob_get_clean();
        @ob_end_clean(); // Clean the outer buffer silently
        return $output;
    }
    
    /**
     * Test send_json with success status code sets ok to true
     */
    public function test_send_json_with_success_status_code_sets_ok_to_true() {
        $output = $this->capture_json_output( [ 200, 'Success message' ] );
        $response = json_decode( $output, true );
        
        $this->assertTrue( $response['ok'] );
    }
    
    /**
     * Test send_json with error status code sets ok to false
     */
    public function test_send_json_with_error_status_code_sets_ok_to_false() {
        $output = $this->capture_json_output( [ 400, 'Bad request' ] );
        $response = json_decode( $output, true );
        
        $this->assertFalse( $response['ok'] );
    }
    
    /**
     * Test send_json with redirect status code (3xx) sets ok to false
     */
    public function test_send_json_with_redirect_status_code_sets_ok_to_false() {
        $output = $this->capture_json_output( [ 301, 'Moved permanently' ] );
        $response = json_decode( $output, true );
        
        $this->assertFalse( $response['ok'] );
    }
    
    /**
     * Test send_json converts string message to array
     */
    public function test_send_json_converts_string_message_to_array() {
        $output = $this->capture_json_output( [ 200, 'Single message' ] );
        $response = json_decode( $output, true );
        
        $this->assertIsArray( $response['output'] );
        $this->assertCount( 1, $response['output'] );
        $this->assertEquals( 'Single message', $response['output'][0] );
    }
    
    /**
     * Test send_json keeps array message as array
     */
    public function test_send_json_keeps_array_message_as_array() {
        $messages = [ 'Message one', 'Message two', 'Message three' ];
        
        $output = $this->capture_json_output( [ 200, $messages ] );
        $response = json_decode( $output, true );
        
        $this->assertIsArray( $response['output'] );
        $this->assertCount( 3, $response['output'] );
        $this->assertEquals( $messages, $response['output'] );
    }
    
    /**
     * Test send_json includes data in response
     */
    public function test_send_json_includes_data_in_response() {
        $data = [
            'user_id' => 123,
            'email' => 'test@example.com',
            'name' => 'Test User'
        ];
        
        $output = $this->capture_json_output( [ 200, 'Success' ], $data );
        $response = json_decode( $output, true );
        
        $this->assertEquals( $data, $response['data'] );
    }
    
    /**
     * Test send_json with empty data array
     */
    public function test_send_json_with_empty_data_array() {
        $output = $this->capture_json_output( [ 200, 'Success' ] );
        $response = json_decode( $output, true );
        
        $this->assertIsArray( $response['data'] );
        $this->assertEmpty( $response['data'] );
    }
    
    /**
     * Test send_json with multiple messages
     */
    public function test_send_json_with_multiple_messages() {
        $messages = [
            'Form submitted successfully',
            'Email sent to admin',
            'User notified'
        ];
        
        $output = $this->capture_json_output( [ 200, $messages ] );
        $response = json_decode( $output, true );
        
        $this->assertCount( 3, $response['output'] );
        $this->assertEquals( 'Form submitted successfully', $response['output'][0] );
        $this->assertEquals( 'Email sent to admin', $response['output'][1] );
        $this->assertEquals( 'User notified', $response['output'][2] );
    }
    
    /**
     * Test send_json handles non-array status with default 500 error
     */
    public function test_send_json_handles_non_array_status_with_default_500_error() {
        $output = $this->capture_json_output( 'not an array' );
        $response = json_decode( $output, true );
        
        $this->assertFalse( $response['ok'] );
        $this->assertIsArray( $response['output'] );
        $this->assertCount( 1, $response['output'] );
        $this->assertEquals( 'Service produced an unknown reponse. Your request may have failed.', $response['output'][0] );
    }
    
    /**
     * Test send_json response is valid JSON
     */
    public function test_send_json_response_is_valid_json() {
        $output = $this->capture_json_output( [ 200, 'Success' ] );
        $response = json_decode( $output, true );
        
        $this->assertEquals( JSON_ERROR_NONE, json_last_error() );
        $this->assertIsArray( $response );
    }
    
    /**
     * Test send_json clears output buffer
     */
    public function test_send_json_clears_output_buffer() {
        ob_start();
        ob_start(); // Double buffer
        
        // Add some content to output buffer (simulating PHPMailer debug output)
        echo "SMTP -> FROM SERVER:220 smtp.example.com ESMTP";
        echo "SMTP -> FROM SERVER:250-smtp.example.com";
        
        HTTP_Response::send_json( [ 200, 'Success' ] );
        $output = ob_get_clean();
        @ob_end_clean();
        
        // Output should only contain JSON, not the SMTP debug info
        $this->assertStringNotContainsString( 'SMTP', $output );
        $this->assertStringNotContainsString( 'FROM SERVER', $output );
        
        // Should be valid JSON
        $response = json_decode( $output, true );
        $this->assertIsArray( $response );
    }
    
    /**
     * Test send_json response structure is correct
     */
    public function test_send_json_response_structure_is_correct() {
        $output = $this->capture_json_output( [ 200, 'Success' ], [ 'id' => 1 ] );
        $response = json_decode( $output, true );
        
        // Check all required keys exist
        $this->assertArrayHasKey( 'ok', $response );
        $this->assertArrayHasKey( 'output', $response );
        $this->assertArrayHasKey( 'data', $response );
        
        // Check types
        $this->assertIsBool( $response['ok'] );
        $this->assertIsArray( $response['output'] );
        $this->assertIsArray( $response['data'] );
    }
    
    /**
     * Test send_json with various 2xx status codes
     */
    public function test_send_json_with_various_2xx_status_codes() {
        $success_codes = [ 200, 201, 202, 204 ];
        
        foreach ( $success_codes as $code ) {
            $output = $this->capture_json_output( [ $code, "Success with code $code" ] );
            $response = json_decode( $output, true );
            
            $this->assertTrue( $response['ok'], "Status code $code should set ok to true" );
        }
    }
    
    /**
     * Test send_json with various 4xx status codes
     */
    public function test_send_json_with_various_4xx_status_codes() {
        $client_error_codes = [ 400, 401, 403, 404, 422 ];
        
        foreach ( $client_error_codes as $code ) {
            $output = $this->capture_json_output( [ $code, "Client error $code" ] );
            $response = json_decode( $output, true );
            
            $this->assertFalse( $response['ok'], "Status code $code should set ok to false" );
        }
    }
    
    /**
     * Test send_json with various 5xx status codes
     */
    public function test_send_json_with_various_5xx_status_codes() {
        $server_error_codes = [ 500, 502, 503 ];
        
        foreach ( $server_error_codes as $code ) {
            $output = $this->capture_json_output( [ $code, "Server error $code" ] );
            $response = json_decode( $output, true );
            
            $this->assertFalse( $response['ok'], "Status code $code should set ok to false" );
        }
    }
    
    /**
     * Test send_json handles complex data structures
     */
    public function test_send_json_handles_complex_data_structures() {
        $complex_data = [
            'user' => [
                'id' => 123,
                'email' => 'test@example.com',
                'meta' => [
                    'role' => 'admin',
                    'permissions' => [ 'read', 'write', 'delete' ]
                ]
            ],
            'form' => [
                'id' => 456,
                'fields' => [
                    [ 'name' => 'email', 'type' => 'text' ],
                    [ 'name' => 'message', 'type' => 'textarea' ]
                ]
            ]
        ];
        
        $output = $this->capture_json_output( [ 200, 'Success' ], $complex_data );
        $response = json_decode( $output, true );
        
        $this->assertEquals( $complex_data, $response['data'] );
        $this->assertEquals( 123, $response['data']['user']['id'] );
        $this->assertCount( 3, $response['data']['user']['meta']['permissions'] );
    }
}
