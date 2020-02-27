<?php


namespace App\Handlers\Admin\Servers;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\Servers\BeforeDeleteServerEvent;
use App\Events\Admin\Servers\DeleteServerEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;

class DeleteHandler
{
    private $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id, false);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $admin, int $id): void
    {
        $server = $this->getServer($id);

        event(new BeforeDeleteServerEvent($server));

        $this->serverRepository->delete($server);

        event(new DeleteServerEvent($admin, $server));
    }
}