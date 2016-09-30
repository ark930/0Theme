<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private $users;
    private $tags;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // generate user data
        $this->users = factory(App\Models\User::class, 20)->create();

        // generate tag data
        $this->tags = factory(App\Models\Tag::class, 20)->create();

        // generate theme data
        factory(App\Models\Theme::class, 10)->create()->each(function($theme) {
            // associate to theme version
            $theme->versions()->saveMany(factory(App\Models\ThemeVersion::class, random_int(2, 5))->make());

            // create a theme version and associate current version to it
            $themeVersion = factory(App\Models\ThemeVersion::class)->make();
            $theme->versions()->save($themeVersion);
            $theme->currentVersion()->associate($themeVersion);
            $theme->save();

            // attach tags to theme version
            $tags = clone $this->tags;
            $randomTag = null;
            for($i = 0; $i < random_int(1, 3); $i++) {
                $j = random_int(0, $this->tags->count() -1 );
                $randomTag = $tags->pull($j);
                $theme->tags()->attach($randomTag);
            }

            // generate theme download data
            $theme->versions()->each(function($themeVersion) {
                $themeDownloads = factory(App\Models\ThemeDownload::class, random_int(2, 10))->make()->each(function($themeDownload) {
                    // associate download to specific user
                    $user = $this->users->random();
                    $themeDownload->user()->associate($user);
                });

                // associate download to specific theme version
                $themeVersion->downloads()->saveMany($themeDownloads);
            });

            // generate user theme data
            $user = clone $this->users;
            $randomUser = null;
            echo $this->users->count() . ' ';
            for($i = 0; $i < random_int(1, 10); $i++) {
                $j = random_int(0, $this->users->count() -1 );
                $randomUser = $user->pull($j);
                $theme->users()->attach($randomUser, [
                    'activate_at' =>  date('Y-m-d H:i-s'),
                    'expire_at' => date('Y-m-d H:i-s'),
                    'theme_key' => str_random(24),
                    'is_activate' => random_int(0, 1),
                    'deactivate_reason' => 'deactivate_reason',
                ]);
            }
        });

    }
}
