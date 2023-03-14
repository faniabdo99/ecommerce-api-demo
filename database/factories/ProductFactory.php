<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use App\User;
use App\Store;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Product::class, function (Faker $faker) {

    return [
        'title' => $faker->title,
        'description' => $faker->sentence,
        'price' => 150,
        'vat_percentage' => 14 ,
        'is_vat_included' => 1 ,
        'user_id' => factory(User::class)->create()->id,
        'store_id' => factory(Store::class)->create()->id
    ];
});
