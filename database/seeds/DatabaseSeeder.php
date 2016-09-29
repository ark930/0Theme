<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

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
//        Model::unguard();

        // generate user data
        $this->users = factory(App\Models\User::class, 5)->create();

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
//            $theme->versions()->each(function($themeVersion) {
//                $themeDownload = factory(App\Models\ThemeDownload::class, random_int(2, 10))->make();
//
//                $user = $this->users->random();
//                $user = \App\Models\User::find($user['id']);
////                echo $user['id'];
////                $user->downloads()->save($themeDownload);
//                $themeDownload->user()->associate($user);
////                $themeDownload->save();
////                $themeVersion->downloads()->saveMany($themeDownload);
//            });
        });


//        Model::reguard();

    }
}
