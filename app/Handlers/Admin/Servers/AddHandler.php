<?php


namespace App\Handlers\Admin\Servers;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\Servers\AddServerEvent;
use App\Repository\Site\Server\ServerRepository;

class AddHandler
{
    private $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    public function handle(User $admin, string $name, string $ip, int $port, int $rconPort): Server
    {
        $server = new Server(
            $name,
            $ip,
            $port,
            $rconPort
        );

        $this->serverRepository->create($server);

        event(new AddServerEvent($admin, $server));

        return $server;
    }
}