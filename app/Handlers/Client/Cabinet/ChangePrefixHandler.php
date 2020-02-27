<?php


namespace App\Handlers\Client\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Client\Cabinet\ChangePrefixEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Cabinet\Prefix\PrefixChanger;
use App\Services\Cabinet\Prefix\PrefixSuffix;
use App\Services\Permissions\Permissions;

class ChangePrefixHandler
{
    private $serverRepository;

    private $prefixChanger;

    public function __construct(ServerRepository $serverRepository, PrefixChanger $prefixChanger)
    {
        $this->serverRepository = $serverRepository;
        $this->prefixChanger = $prefixChanger;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $user, PrefixSuffix $prefix, int $serverId): void
    {
        $server = $this->getServer($serverId);

        if (!$user->permissions()->hasPermission(Permissions::CABINET_PREFIX, $server)) {
            throw new Exception('Вы не можете изменять префикс!');
        }

        $this->prefixChanger->change($user, $server, $prefix);

        event(new ChangePrefixEvent($user, $server, $prefix));
    }
}