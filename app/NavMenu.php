<?php


namespace App;


use App\Repository\Site\Application\ApplicationRepository;
use App\Repository\Site\BugReport\BugReportRepository;
use App\Services\Auth\Auth;
use App\Services\Permissions\Permissions;

class NavMenu
{
    public static $active = 'index';

    private static $activeFirst;

    public static function active_tab(string $name): string
    {
        return self::$active == $name ? 'active' : '';
    }

    public static function active_menu($name, string $class = 'active'): string
    {
        if(is_null(self::$activeFirst)) {
            self::$activeFirst = explode('.', self::$active)[0];
        }

        if(is_array($name)) {
            return in_array(self::$activeFirst, $name) ? $class : '';
        }
        return self::$activeFirst == $name ? $class : '';
    }

    public static function getCountWaitApplications(): int
    {
        $servers = null;
        if (!Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_APPLICATIONS_VIEW_ALL)) {
            $servers = Auth::getUser()->permissions()->getServersWithPermission(Permissions::MP_APPLICATIONS_VIEW);
        }

        /* @var ApplicationRepository $repository */
        $repository = app()->make(ApplicationRepository::class);

        return $repository->getCountWait($servers);
    }

    public static function getCountWaitBugReports(): int
    {
        $servers = null;

        /* @var BugReportRepository $repository */
        $repository = app()->make(BugReportRepository::class);

        return $repository->getCountWait($servers);
    }
}