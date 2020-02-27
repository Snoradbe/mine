<?php


namespace App\Handlers\Admin\Team;


use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
use App\Helpers\GroupsHelper;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\UserAdminGroup\UserAdminGroupRepository;
use App\Services\Permissions\Permissions;

class ListHandler
{
    private $userAdminGroupRepository;

    private $serverRepository;

    private $groupRepository;

    public function __construct(
        UserAdminGroupRepository $userAdminGroupRepository,
        ServerRepository $serverRepository,
        GroupRepository $groupRepository)
    {
        $this->userAdminGroupRepository = $userAdminGroupRepository;
        $this->serverRepository = $serverRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Список администрации БЕЗ масс
     *
     * @param User $admin
     * @param array $servers
     * @param $allowedGroups
     * @return array
     */
    private function getListOnServer(User $admin, array &$servers, $allowedGroups): array
    {
        $canAllServers = $admin->permissions()->hasMPPermission(Permissions::MP_TEAM_VIEW_ALL);
        $canUpgrade = $admin->permissions()->hasMPPermission(Permissions::MP_TEAM_UPGRADE);
        $list = [];

        if (!$canAllServers) {
            $allowedServers = $admin->permissions()->getServersWithPermission(Permissions::MP_TEAM_VIEW);
            if (!is_null($allowedServers)) {
                $servers = $allowedServers;
            }
        }

        $users = $this->userAdminGroupRepository->getAll();

        foreach ($servers as $server)
        {
            if (!isset($list[$server->getId()])) {
                $upgradeGroups = [];
                if ($canUpgrade) {
                    if ($canAllServers) {
                        $upgradeGroups = $allowedGroups;
                    } else {
                        $adminGroup = $admin->getAdminGroup($server);
                        if (!is_null($adminGroup) && !is_null($adminGroup->getGroup()->getParent())) {
                            GroupsHelper::walkParent($adminGroup->getGroup()->getParent(), $upgradeGroups);
                        }
                    }
                }
                $list[$server->getId()] = [
                    'server' => $server,
                    'users' => array_filter($users, function (UserAdminGroup $userAdminGroup) use ($server) {
                        return $userAdminGroup->getServer() === $server;
                    }),
                    'groups' => $upgradeGroups
                ];
            }
        }

        return $list;
    }

    /**
     * Список администрации с масс
     *
     * @return UserAdminGroup[]
     */
    private function getListMass(): array
    {
        return array_filter($this->userAdminGroupRepository->getAll(), function (UserAdminGroup $userAdminGroup) {
            return is_null($userAdminGroup->getServer());
        });
    }

    public function handle(User $admin): array
    {
        $isAdmin = $admin->permissions()->hasMPPermission(Permissions::ALL);

        $servers = $this->serverRepository->getAll(!$isAdmin);
        $allowedGroups = $admin->permissions()->hasMPPermission(Permissions::ALL)
            ? $this->groupRepository->getAllAdmin()
            : GroupsHelper::getAllowedManageGroups($admin, null, $this->groupRepository->getAllAdmin());

        $list = $this->getListOnServer($admin, $servers, $allowedGroups);
        $listMass = $admin->permissions()->hasMPPermission(Permissions::MP_TEAM_VIEW_ALL)
            ? $this->getListMass()
            : [];

        return [$list, $listMass, $servers, $allowedGroups];
    }
}