<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase {
    use RefreshDatabase;
    /**
     * A basic test to ensure the products system is up.
     *
     * @return void
     */
    public function testUserCanGetAllProducts() {
        $User = User::factory()->create([
            'type' => 'merchant'
        ]);

        $Products = Product::factory()->count(10)->create([
            'user_id' => $User->id,
            'store_id' => Store::factory()->create(['user_id' => $User->id])
        ]);
        $this->actingAs($User);
        $response = $this->get('/api/v1/product', [
            'Authorization' => 'Bearer '.$User->api_token
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 200,
            'success' => true,
            'response' => $Products->toArray()
        ]);
    }
    public function testUserCanGetSingleProduct(){
        $User = User::factory()->create([
            'type' => 'merchant'
        ]);
        $Products = Product::factory()->count(10)->create([
            'user_id' => $User->id,
            'store_id' => Store::factory()->create(['user_id' => $User->id])
        ]);
        $this->actingAs($User);
        $response = $this->get('/api/v1/product/'.$Products->first()->id, [
            'Authorization' => 'Bearer '.$User->api_token
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 200,
            'success' => true,
            'response' => $Products->first()->toArray()
        ]);
    }
}


