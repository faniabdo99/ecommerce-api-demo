<?php

namespace Tests\Feature;

use Tests\TestCase;

class HelloWorldTest extends TestCase
{
    /**
     * A basic test to ensure the systems are up.
     *
     * @return void
     */
    public function testHelloWorld()
    {
        $response = $this->get('/');
        // Ensure the status is as expected "200 (Success)"
        $response->assertStatus(200);
        // Ensure the response is a valid JSON with the expected content
        $response->assertJson(['response' => 'Hello world!']);
    }
}
