<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 2016/10/23
 * Time: 下午4:11
 */

namespace App\Services;


use App\Exceptions\ServiceException;
use App\Repositories\ThemeRepository;

class ThemeService
{
    protected $themeRepository;

    public function __construct(ThemeRepository $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    public function getThemeDownloadPath($theme_id)
    {
        $theme = $this->themeRepository->get($theme_id);
        $themeDownloadPath = $this->themeRepository->getThemeDownloadUrl($theme);
        if($this->themeRepository->verifyBySHA256($theme, $themeDownloadPath)) {
            return $themeDownloadPath;
        }

        throw new ServiceException('Theme file verify failure');
    }
}