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

$factory->define(App\Models\AdminUser::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'),
    ];
});

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    $memberships = ['free', 'basic', 'pro', 'lifetime'];

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'),
        'registered' => rand(0, 1),
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
    ];
});

$factory->define(App\Models\ThemeVersion::class, function (Faker\Generator $faker) {
    return [
        'author' => $faker->name,
        'sha1' => $faker->sha1,
        'version' => '1.0.1',
        'requirements' => $faker->sentence,
        'document_url' => $faker->url,
        'has_free' => rand(0, 1),
        'free_url' => $faker->url,
        'description' => $faker->sentence,
        'changelog' => $faker->sentence,
        'thumbnail' => $faker->url,
        'thumbnail_tiny' => $faker->url,
        'release_at' => date('Y-m-d H:i-s'),
        'store_at' => $faker->url,
        'store_type' => 'local',
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

$factory->define(App\Models\ThemeVersionShowcase::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'title' => $faker->sentence,
    ];
});

$factory->define(App\Models\Order::class, function (Faker\Generator $faker) {
    $status = ['unpay', 'paid', 'refunded'];

    return [
        'order_no' => str_random(10),
        'payment_type' => 'paypal',
        'payment_id' => rand(10000, 100000),
        'price' => rand(100, 10000) / 100,
        'pay_amount' => rand(100, 10000) / 100,
        'paid_amount' => rand(100, 10000) / 100,
        'refund_amount' => rand(100, 10000) / 100,
        'status' => $status[array_rand($status)],
    ];
});