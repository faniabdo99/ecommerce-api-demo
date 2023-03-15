<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class ProductFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'description' => $this->faker->sentence,
            'price' => 150,
            'vat_percentage' => 14 ,
            'is_vat_included' => 1 ,
            'user_id' => User::factory()->create()->id,
            'store_id' => Store::factory()->create()->id
        ];
    }
}
