<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\User;

class SignupTest extends TestCase{
    use RefreshDatabase;
    /**
     * Feature test to ensure the signup is working correctly
     *
     * @return void
     */
    public function testSignupWithEmptyInput(){
        $response = $this->post('/api/v1/auth/signup');
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 422,
            'success' => false,
            'response' => [
              "name" => ["The name field is required."],
              "email" => ["The email field is required."],
              "password" => ["The password field is required."],
            ]
        ]);
    }


    public function testSignupWithInvalidInput(){
        $response = $this->post('/api/v1/auth/signup',[
            'name' => 'name', // Less than 5 characters
            'email' => 'email', // Not a valid email
            'password' => 'pass' // Less than 5 characters
        ]);
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 422,
            'success' => false,
            'response' => [
                "name" => ["The name must be at least 5 characters."],
                "email" => ["The email must be a valid email address."],
                "password" => ["The password must be at least 5 characters."],
            ]
        ]);
    }


    public function testSignupWithValidInput(){
        $response = $this->post('/api/v1/auth/signup',[
            'name' => 'VALID_NAME',
            'email' => 'valid@email.com',
            'password' => 'VALID_PASSWORD'
        ]);
        $response->assertStatus(200);
        // Fetch the newly created user
        $User = User::first();
        $ExpectedUserData = [
            'name' => $User->name,
            'email' => $User->email,
            'updated_at' => $User->updated_at->toIsoString(),
            'created_at' => $User->created_at->toIsoString(),
            'api_token' => $User->api_token,
            'id' => $User->id
        ];
        // Ensure the password has been properly encrypted
        $this->assertTrue(Hash::check('VALID_PASSWORD', $User->password));
        // Match the API response
        $response->assertJson([
            'status' => 200,
            'success' => true,
            'response' => [
                'user' => $ExpectedUserData
            ]
        ]);
        /*
         * Ensure the database has the user record, However at this point we already know for sure that the record
         * has been created since we are using User::first(), but I like to cover the database testing as well just
         * to be extra sure
         */
        $this->assertDatabaseCount('users' , 1);
    }

}
