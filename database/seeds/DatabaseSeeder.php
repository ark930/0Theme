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
        factory(App\Models\Theme::class, 5)->create()->each(function($theme) {
            // associate to theme version
            $theme->versions()->saveMany(factory(App\Models\ThemeVersion::class, random_int(2, 5))->make());

            // create a theme version and associate current version to it
            $themeVersion = factory(App\Models\ThemeVersion::class)->make();
            $theme->versions()->save($themeVersion);
            $theme->currentVersion()->associate($themeVersion);
            $theme->save();

            // attach tags to theme version
            $tags = $this->tags;
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
                    $user = \App\Models\User::find($user['id']);
                    $themeDownload->user()->associate($user);
                });

                // associate download to specific theme version
                $themeVersion->downloads()->saveMany($themeDownloads);
            });
        });

    }
}
