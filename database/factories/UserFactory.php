<?php

use Faker\Generator as Faker;

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

$factory->define(YallaTalk\Models\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(YallaTalk\Models\Topic::class, function (Faker $faker) {
    static $password;

    return [
        'topic_name' => $faker->name,
    ];
});

$factory->define(YallaTalk\Models\Language::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
    ];
});
