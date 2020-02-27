<?php


namespace App\Services\Game\GameMoney;


use App\Entity\Site\Server;
use App\Exceptions\Exception;
use App\Repository\Game\GameMoney\Fe\DoctrineFeRepository;
use App\Services\Settings\DataType;

class GameMoneyManagerFactory
{
    private function __construct(){}

    public static function getManager(Server $server): GameMoneyManager
    {
        $settings = settings('game_money', DataType::JSON, []);
        if (empty($settings)) {
            throw new Exception('Settings game_money is empty!');
        }

        if (!isset($settings['manager']) || empty($settings['manager']) || !is_array($settings['manager'])) {
            throw new Exception('Settings mage_money.manager is not valid!');
        }

        $manager = $settings['manager'][$server->getId()] ?? $settings['manager']['default'];
        if (empty($settings)) {
            throw new Exception('Settings game_money.manager is empty!');
        }

        return new $manager(doctrine_connection(
            DoctrineFeRepository::class,
            $manager::getEntityClassname(),
            'server_' . $server->getId()
        ));
    }
}