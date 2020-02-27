<?php


namespace App\Services\Cabinet\Cloak;


use App\Services\Cabinet\CabinetEnum;
use App\Services\Cabinet\CabinetSettings;

final class Image
{
    private function __construct() {}

    public static function absolutePath(?string $username = null): string
    {
        if (is_null($username)) {
            //return public_path(CabinetSettings::getSkinCloakPath(CabinetEnum::CLOAK_TYPE));
            return CabinetSettings::getSkinCloakPath(CabinetEnum::CLOAK_TYPE);
        }

        $path = self::filename(self::absolutePath(), $username);

        /*return file_exists($path) && is_readable($path)
            ? $path : public_path(CabinetSettings::getSkinCloakPath(CabinetEnum::CLOAK_TYPE) . '/default.png');*/
        return file_exists($path) && is_readable($path)
            ? $path : CabinetSettings::getSkinCloakPath(CabinetEnum::CLOAK_TYPE) . '/default.png';
    }

    public static function getAbsolutePath(string $username): string
    {
        return self::filename(self::absolutePath(), $username);
    }

    private static function filename(string $path, string $username)
    {
        return $path . "/{$username}.png";
    }
}