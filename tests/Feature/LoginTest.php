<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase{
    use RefreshDatabase;
    /**
     * Feature test to ensure the login is working correctly
     *
     * @return void
     */
    public function testLoginWithEmptyInput(){
        $response = $this->post('/api/v1/auth/login');
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 422,
            'success' => false,
            'response' => [
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
            ]
        ]);
    }

    public function testLoginWithInvalidInput(){
        $response = $this->post('/api/v1/auth/login', [
            'email' => 'wrong@email.com',
            'password' => 'WRONG_PASSWORD'
        ]);
        $response->assertStatus(401);
        $response->assertJson([
            'status' => 401,
            'success' => false,
            'response' => 'The email or password you entered are incorrect!'
        ]);
    }

    public function testLoginWithValidInput(){
        $User = User::factory()->create();
        $response = $this->post('/api/v1/auth/login', [
            'email' => $User->email,
            'password' => 'password'
        ]);
        $ExpectedResponse = [
            'user' => [
                'name' => $User->name,
                'email' => $User->email,
                'updated_at' => $User->updated_at->toIsoString(),
                'created_at' => $User->created_at->toIsoString(),
                'api_token' => $User->api_token,
                'id' => $User->id,
                'email_verified_at' => $User->email_verified_at->toIsoString(),
                'type' => $User->type
            ],
            'token' => $User->api_token
        ];
        $this->assertAuthenticated();
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 200,
            'success' => true,
            'response' => $ExpectedResponse
        ]);
    }


}
