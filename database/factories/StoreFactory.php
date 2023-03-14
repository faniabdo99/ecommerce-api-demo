<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Store;
use App\User;
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

$factory->define(Store::class, function (Faker $faker) {
    return [
        'title' => $faker->name."'s Store",
        'user_id' => factory(User::class)->create()->id,
    ];
});