<?php


namespace App\Handlers\Admin\Shop\Player\Warehouse;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Repository\Game\Shop\RealMine\RealMineRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\User\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ListHandler
{
    private $userRepository;

    private $serverRepository;

    private $realMineRepository;

    public function __construct(UserRepository $userRepository, ServerRepository $serverRepository, RealMineRepository $realMineRepository)
    {
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
        $this->realMineRepository = $realMineRepository;
    }

    private function getUser(string $name): User
    {
        $user = $this->userRepository->findByName($name);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id, false);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(string $name, int $serverId, int $page): LengthAwarePaginator
    {
        $user = $this->getUser($name);
        $server = $this->getServer($serverId);

        return $this->realMineRepository->getAllByUser($user, $server, $page);
    }
}