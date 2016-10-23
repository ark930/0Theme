<?php

namespace App\Repositories;


use App\Models\Theme;

class ThemeRepository
{
    public function getThemeDownloadUrl($theme)
    {
        $basePath = '';
        $themePath = $theme->currentVersion['store_at'];
        $themeFullPath = sprintf('%s/%s', $basePath, $themePath);

        return $themeFullPath;
    }

    public function verifyBySHA256($theme, $themeDownloadPath)
    {
        if(hash_file('sha256', $themeDownloadPath) == $theme->currentVersion['sha256']) {
            return true;
        }

        return false;
    }

    public function get($id)
    {
        $theme = Theme::findOrFail($id);

        return $theme;
    }
}