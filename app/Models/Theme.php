<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'name', 'author',
    ];

    public function currentVersion()
    {
        return $this->belongsTo('App\Models\ThemeVersion', 'current_version_id');
    }

    public function versions()
    {
        return $this->hasMany('App\Models\ThemeVersion');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'theme_tags', 'theme_id', 'tag_id');
    }

    public function categoryTags()
    {
        return $this->tags->where('type', 'theme_category')->implode('name', '/');
    }

    public function typeTags()
    {
        return $this->tags->where('type', 'theme_type')->implode('name', '/');
    }

    public function tagsCount()
    {
        $tags = $this->tags;

        $typeCount = $tags->where('type', 'theme_type')->count();
        $categoryCount = $tags->where('category', 'theme_category')->count();

        return [
            'type' => $typeCount,
            'category' => $categoryCount,
        ];
    }
}
