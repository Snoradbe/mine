<?php


namespace App\Services\Cabinet\Skin;


use App\Exceptions\Exception;
use App\Services\Cabinet\CabinetEnum;
use App\Services\Cabinet\CabinetSettings;

final class Image
{
    private function __construct() {}

    public static function absolutePath(?string $username = null): string
    {
        if (is_null($username)) {
            //return public_path(CabinetSettings::getSkinCloakPath(CabinetEnum::SKIN_TYPE));
            return CabinetSettings::getSkinCloakPath(CabinetEnum::SKIN_TYPE);
        }

        $path = self::filename(self::absolutePath(), $username);

        /*return file_exists($path) && is_readable($path)
            ? $path : public_path(CabinetSettings::getSkinCloakPath(CabinetEnum::SKIN_TYPE) . '/default.png');*/
        return file_exists($path) && is_readable($path)
            ? $path : CabinetSettings::getSkinCloakPath(CabinetEnum::SKIN_TYPE) . '/default.png';
    }

    public static function getAbsolutePath(string $username): string
    {
        return self::filename(self::absolutePath(), $username);
    }

    public static function saveSkinHead(string $username, int $size = 32)
    {
        $skinFile = self::getAbsolutePath($username);

        if (!is_file($skinFile)) {
            throw new Exception('File not found!');
        }

        $resultPath = self::absolutePath() . '/heads';
        if (!is_dir($resultPath)) {
            mkdir($resultPath, 755);
        }
        $resultPath .= '/' . $username . '.png';

        $skin = imagecreatefrompng($skinFile);

        $skinSize = getimagesize($skinFile);
        $h = $skinSize[0];
        $ratio = $h / 64;

        $preview = imagecreatetruecolor($size, $size);

        imagecopyresized(
            $preview,
            $skin,
            0 * $ratio,
            0 * $ratio,
            8 * $ratio,
            8 * $ratio,
            $size,
            $size,
            8 * $ratio,
            8 * $ratio
        );

        imagepng($preview, $resultPath, 3);

        @imagedestroy($skin);
        @imagedestroy($preview);
    }

    public static function deleteSkinHead(string $username): void
    {
        $resultPath = self::absolutePath() . '/heads/' . $username . '.png';
        if (is_file($resultPath)) {
            @unlink($resultPath);
        }
    }

    private static function filename(string $path, string $username)
    {
        return $path . "/{$username}.png";
    }
}