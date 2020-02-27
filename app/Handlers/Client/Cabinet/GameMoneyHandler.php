<?php


namespace App\Handlers\Client\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Client\Cabinet\GameMoneyEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\User\UserRepository;
use App\Services\Game\GameMoney\GameMoneyManagerFactory;
use App\Services\Game\GameMoney\GameMoneySettings;

class GameMoneyHandler
{
    private $serverRepository;

    private $userRepository;

    public function __construct(ServerRepository $serverRepository, UserRepository $userRepository)
    {
        $this->serverRepository = $serverRepository;
        $this->userRepository = $userRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $user, int $serverId, int $amount): float
    {
        $server = $this->getServer($serverId);
        $rate = GameMoneySettings::getRate($server);

        $price = $amount * $price;

        if (!$user->hasMoney($price)) {
            throw new Exception('Недостаточно средств на балансе!');
        }

        $manager = GameMoneyManagerFactory::getManager($server);

        $entity = $manager->getMoneyEntity($user);
        if (is_null($entity)) {
            throw new Exception('Вы еще не заходили на этот сервер, поэтому обмен невозможен!');
        }

        $user->withdrawMoney($price);
        $this->userRepository->update($user);

        $entity->setMoney($entity->getMoney() + ($amount * $rate));
        $manager->update($entity);

        event(new GameMoneyEvent($user, $server, $price, 'rub', $amount));

        return $entity->getMoney();
    }
}