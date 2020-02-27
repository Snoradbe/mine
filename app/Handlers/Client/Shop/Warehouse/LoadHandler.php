<?php


namespace App\Handlers\Client\Shop\Warehouse;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Repository\Game\Shop\RealMine\RealMineRepository;
use App\Repository\Site\Server\ServerRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class LoadHandler
{
    private $serverRepository;

    private $realMineRepository;

    public function __construct(ServerRepository $serverRepository, RealMineRepository $realMineRepository)
    {
        $this->serverRepository = $serverRepository;
        $this->realMineRepository = $realMineRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $user, int $serverId, int $page): LengthAwarePaginator
    {
        $server = $this->getServer($serverId);

        return $this->realMineRepository->getAllByUser($user, $server, $page);
    }
}