<?php


namespace App\Services\Game\GameMoney;


use App\Entity\Site\Server;
use App\Exceptions\Exception;
use App\Services\Settings\DataType;

class GameMoneySettings
{
    private static $cache = [];

    private function __construct(){}

    private static function getSettings(string $key, ?string $castTo = null, $default = null)
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        $setting = settings($key, $castTo, $default);
        static::$cache[$key] = $setting;

        return $setting;
    }

    public static function getManagers(): array
    {
        return self::getSettings('game_money', DataType::JSON, [])['manager'] ?? [];
    }

    public static function getManagerClass(Server $server): string
    {
        $settings = self::getSettings('game_money', DataType::JSON, []);
        if (empty($settings)) {
            throw new Exception('Settings game_money is empty!');
        }

        if (!isset($settings['manager']) || empty($settings['manager']) || !is_array($settings['manager'])) {
            throw new Exception('Settings game_money.manager is not valid!');
        }

        $manager = $settings['manager'][$server->getId()] ?? $settings['manager']['default'];
        if (empty($settings)) {
            throw new Exception('Settings game_money.manager is empty!');
        }

        return $manager;
    }

    public static function getRates(): array
    {
        return self::getSettings('game_money', DataType::JSON, [])['rate'] ?? [];
    }

    public static function getRate(Server $server): float
    {
        $settings = self::getSettings('game_money', DataType::JSON, []);
        if (empty($settings)) {
            throw new Exception('Settings game_money is empty!');
        }

        if (!isset($settings['rate']) || empty($settings['rate']) || !is_array($settings['rate'])) {
            throw new Exception('Settings mage_money.rate is not valid!');
        }

        $rate = $settings['rate'][$server->getId()] ?? $settings['rate']['default'];

        if (empty($rate)) {
            throw new Exception('Settings game_money.rate is empty!');
        }

        return $rate;
    }
}