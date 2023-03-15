<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test to ensure the store system is up.
     *
     * @return void
     */
    public function testCreateStoreWithValidInput() {
        $User = User::factory()->create([
            'type' => 'merchant'
        ]);
        $response = $this->post('/api/v1/store/create', [
            'vat_percentage' => 14,
            'shipping' => 10
        ],[
            'Authorization' => 'Bearer '.$User->api_token
        ]);
        // Ensure the status is as expected "200 (Success)"
        $response->assertStatus(201);
        // Ensure the response is a valid JSON with the expected content
        $Store = Store::first();
        $response->assertJson([
            'status' => 201,
            'success' => true,
            'response' => $Store->toArray()
        ]);
    }
    public function testCreateStoreWithStoreAlreadyCreated() {
        $Store = Store::factory()->create();
        $User = $Store->User;
        $response = $this->post('/api/v1/store/create', [],[
            'Authorization' => 'Bearer '.$User->api_token
        ]);
        // Ensure the status is as expected "200 (Success)"
        $response->assertStatus(406);
        // Ensure the response is a valid JSON with the expected content
        $response->assertJson([
            'status' => 406,
            'success' => false,
            'response' => 'You already have a store!'
        ]);
    }
}
