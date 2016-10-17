<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumUser extends Model
{
    protected $connection = 'mysql_forum';
    protected $table = 'users';
    public $timestamps = false;

    public static function newUser($username, $email, $join_time)
    {
        $forumUser = new self;
        $forumUser['username'] = $username;
        $forumUser['email'] = $email;
        $forumUser['is_activated'] = 1;
        $forumUser['join_time'] = $join_time;
        $forumUser->save();
    }
}
