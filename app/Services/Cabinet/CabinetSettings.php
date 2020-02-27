<?php


namespace App\Services\Cabinet;


use App\Exceptions\Exception;
use App\Services\Settings\DataType;

class CabinetSettings
{
    private static $cache = [];

    private static function getSettings(string $key, ?string $castTo = null, $default = null)
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        $setting = settings($key, $castTo, $default);
        static::$cache[$key] = $setting;

        return $setting;
    }

    public static function getDefaultPermissions(): array
    {
        $settings = self::getSettings('cabinet', DataType::JSON, []);

        return $settings['default_permissions'] ?? [];
    }

    public static function getSkinCloakSettings(string $type): array
    {
        $settings = self::getSettings('cabinet', DataType::JSON, []);

        return $settings[$type] ?? [];
    }

    public static function getSkinCloakResolution(string $type, string $type2, bool $hd = false): int
    {
        $settings = self::getSkinCloakSettings($type);

        $w = $hd ? 'hd_' . $type2 : $type2;

        return $settings[$w] ?? 0;
    }

    public static function getSkinCloakSize(string $type): int
    {
        $settings = self::getSkinCloakSettings($type);

        return $settings['size'] ?? 0;
    }

    public static function getSkinCloakPath(string $type): string
    {
        /*$settings = self::getSkinCloakSettings($type);

        if (!isset($settings['path'])) {
            throw new Exception("Путь к $type не задан!");
        }

        return $settings['path'];*/

        return config('site.skin_cloak.path', '') . "/{$type}s";
    }

    public static function getGroupsSettings(bool $primary): array
    {
        $settings = self::getSettings('cabinet', DataType::JSON, []);

        $key = $primary ? 'groups' : 'other_groups';

        return $settings[$key] ?? [];
    }

    public static function getSellingGroups(bool $primary, int $server): array
    {
        $settings = self::getGroupsSettings($primary);

        return $settings[$server] ?? [];
    }

    public static function getPrefixSettings(): array
    {
        $settings = self::getSettings('cabinet', DataType::JSON, []);

        return $settings['prefix'] ?? [];
    }
}