<?php


namespace App\Handlers\Admin\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\Cabinet\ChangePrefixEvent;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\User\UserRepository;
use App\Services\Cabinet\Prefix\PrefixChanger;
use App\Services\Cabinet\Prefix\PrefixSuffix;
use App\Services\Permissions\Permissions;

class ChangePrefixHandler
{
    private $userRepository;

    private $serverRepository;

    private $prefixChanger;

    public function __construct(UserRepository $userRepository, ServerRepository $serverRepository, PrefixChanger $prefixChanger)
    {
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
        $this->prefixChanger = $prefixChanger;
    }

    private function getUser(int $id): User
    {
        $user = $this->userRepository->find($id);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $admin, PrefixSuffix $prefix, int $userId, int $serverId): void
    {
        $target = $this->getUser($userId);
        $server = $this->getServer($serverId);

        if (
            !$admin->permissions()->hasMPPermission(Permissions::MP_CABINET_VIEW_ALL)
            &&
            !is_null($admin->permissions()->getServersWithPermission(Permissions::MP_CABINET_VIEW))
            &&
            !in_array($server, $admin->permissions()->getServersWithPermission(Permissions::MP_CABINET_VIEW))
        ) {
            throw new PermissionDeniedException();
        }

        $this->prefixChanger->change($target, $server, $prefix);

        event(new ChangePrefixEvent($admin, $target, $server, $prefix));
    }
}