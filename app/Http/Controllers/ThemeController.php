<?php

namespace App\Http\Controllers;

use App\Services\ThemeService;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function download(Request $request, $theme_id, ThemeService $themeService)
    {
        $key = $request->input('key');
        if($key != '123') {
            return response('error key', 400);
        }

        $themeDownloadPath = $themeService->getThemeDownloadPath($theme_id);

        return response()->download($themeDownloadPath);
    }
}
