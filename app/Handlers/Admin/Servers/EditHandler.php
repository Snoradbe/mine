<?php


namespace App\Handlers\Admin\Servers;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\Servers\EditServerEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;

class EditHandler
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

    public function handle(User $admin, int $id, string $name, string $ip, int $port, int $rconPort, bool $enabled): void
    {
        $server = $this->getServer($id);
        $old = clone $server;

        $server->setName($name);
        $server->setIp($ip);
        $server->setPort($port);
        $server->setRconPort($rconPort);
        $server->setEnabled($enabled);

        $this->serverRepository->update($server);

        event(new EditServerEvent($admin, $old, $server));
    }
}