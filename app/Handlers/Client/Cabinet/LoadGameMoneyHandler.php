<?php


namespace App\Handlers\Client\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Game\GameMoney\GameMoneyManagerFactory;
use App\Services\Game\GameMoney\GameMoneySettings;

class LoadGameMoneyHandler
{
    private $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $user, int $serverId): array
    {
        $server = $this->getServer($serverId);
        $rate = GameMoneySettings::getRate($server);

        $manager = GameMoneyManagerFactory::getManager($server);

        $entity = $manager->getMoneyEntity($user);
        if (is_null($entity)) {
            throw new Exception('Вы еще не заходили на этот сервер!');
        }

        return [$rate, $entity->getMoney()];
    }
}