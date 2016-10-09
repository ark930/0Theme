<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepositories;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function download(Request $request, $theme_id, UserRepositories $userRepositories)
    {
        $key = $request->input('key');
        if($key != '123') {
            return response('error key', 400);
        }

        $themeDownloadPath = $userRepositories->getThemeDownloadUrl($theme_id);
        if($userRepositories->verifyBySHA1($theme_id, $themeDownloadPath)) {
            return response()->download($themeDownloadPath);
        }

        return abort(400);
//        $path = sprintf('%s/%s', public_path('theme'), '1.pdf');
//        return response()->download($downloadPath);
    }
}
