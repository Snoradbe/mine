<?php


namespace App\Handlers\Admin\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Helpers\FileHelper;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\User\UserRepository;
use App\Services\Cabinet\CabinetUtils;
use App\Services\Cabinet\Prefix\PrefixSuffix;
use App\Services\Permissions\Permissions;

class PlayerInfoHandler
{
    private $userRepository;

    private $serverRepository;

    private $groupRepository;

    public function __construct(UserRepository $userRepository, ServerRepository $serverRepository, GroupRepository $groupRepository)
    {
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
        $this->groupRepository = $groupRepository;
    }

    private function getUser(string $name): User
    {
        $user = $this->userRepository->findByName($name);
        if (is_null($user)) {
            $user = $this->userRepository->findByUUID($name);
            if (is_null($user)) {
                throw new Exception('Игрок не найден!');
            }
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

    public function handle(User $admin, string $name, int $serverId): array
    {
        $target = $this->getUser($name);
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

        $groups = $this->groupRepository->getAllDonate();
        [$prefix, $suffix] = CabinetUtils::getPermissionsManager($server)->getPrefixSuffix($target->getUuid());
        if (empty($prefix) || empty($suffix)) {
            $prefix = PrefixSuffix::createEmpty();
        } else {
            $prefix = PrefixSuffix::createFromPermission($prefix, $suffix);
        }
        $permissions = CabinetUtils::getPermissionsManager($server)->getPermissions($target->getUuid());

        $path = config('site.skin_cloak.path');

        $defaultSkin = null;
        $skin = FileHelper::imageToBase64(
            sprintf($path . '/skins/%s.png', $target->getName())
        );
        if (empty($skin)) {
            $defaultSkin = FileHelper::imageToBase64(
                sprintf($path . '/skins/%s.png', 'default')
            );
        }
        $cloak = FileHelper::imageToBase64(
            sprintf($path . '/cloaks/%s.png', $target->getName())
        );

        return [$target, $server, $groups, $prefix, $permissions, $skin, $cloak, $defaultSkin];
    }
}