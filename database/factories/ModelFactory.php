<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    $memberships = ['basic', 'pro', 'lifetime'];

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'),
        'registered' => random_int(0, 1),
        'membership' => $memberships[array_rand($memberships)],
        'pro_from' => date('Y-m-d H:i-s'),
        'pro_to' => date('Y-m-d H:i-s'),
        'register_at' => date('Y-m-d H:i-s'),
        'first_login_at' => date('Y-m-d H:i-s'),
        'last_login_at' => date('Y-m-d H:i-s'),
        'last_login_ip' => $faker->ipv4,
        'email_confirm_code' => str_random(24),
    ];
});

$factory->define(App\Models\Theme::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'author' => $faker->name,
    ];
});

$factory->define(App\Models\ThemeVersion::class, function (Faker\Generator $faker) {
    return [
        'author' => $faker->name,
        'version' => '1.0.1',
        'description' => $faker->sentence,
        'logo' => $faker->url,
        'release_at' => date('Y-m-d H:i-s'),
        'demo_url' => $faker->url,
        'lite_url' => $faker->url,
        'content' => $faker->sentence,
        'requirement' => $faker->sentence,
        'premium_store_at' => $faker->url,
        'premium_store_type' => 'local',
        'premium_sha1' => $faker->sha1,
    ];
});

$factory->define(App\Models\Tag::class, function (Faker\Generator $faker) {
    $type = ['theme_type', 'theme_category'];

    return [
        'name' => $faker->word,
        'type' => $type[array_rand($type)],
    ];
});

$factory->define(App\Models\ThemeDownload::class, function (Faker\Generator $faker) {
    return [
        'download_at' => date('Y-m-d H:i-s'),
        'ip' => $faker->ipv4,
    ];
});
