<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class StoreFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name."'s Store",
            'vat_percentage' => 14,
            'shipping' => 10,
            'user_id' => User::factory()->create(['type' => 'merchant'])->id,
        ];
    }
}

