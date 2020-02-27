<?php


namespace App\Handlers\Admin\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\Cabinet\RemovePrefixEvent;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\User\UserRepository;
use App\Services\Cabinet\CabinetUtils;
use App\Services\Cabinet\Prefix\PrefixSuffix;
use App\Services\Permissions\Permissions;

class RemovePrefixHandler
{
    private $userRepository;

    private $serverRepository;

    public function __construct(UserRepository $userRepository, ServerRepository $serverRepository)
    {
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
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

    public function handle(User $admin, int $userId, int $serverId): void
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

        [$prefix, $suffix] = CabinetUtils::getPermissionsManager($server)->getPrefixSuffix($target->getUuid());
        if (empty($prefix) && empty($suffix)) {
            throw new Exception('У игрока нет префикса!');
        }

        if (empty($prefix)) {
            $prefix = '';
        }
        if (empty($suffix)) {
            $suffix = '';
        }
        $prefixSuffix = PrefixSuffix::createFromPermission($prefix, $suffix);

        CabinetUtils::getPermissionsManager($server)->removePrefixSuffix($target->getUuid());

        event(new RemovePrefixEvent($admin, $target, $server, $prefixSuffix));
    }
}