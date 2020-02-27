<?php


namespace App\Handlers\Admin\Team;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
use App\Events\Admin\Team\TransitTeamEvent;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Helpers\GroupsHelper;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\User\UserRepository;
use App\Repository\Site\UserAdminGroup\UserAdminGroupRepository;
use App\Services\Cabinet\CabinetUtils;
use App\Services\Forum\ForumManager;
use App\Services\Permissions\Permissions;

class TransitHandler
{
    private $userRepository;

    private $serverRepository;

    private $groupRepository;

    private $userAdminGroupRepository;

    public function __construct(
        UserRepository $userRepository,
        ServerRepository $serverRepository,
        GroupRepository $groupRepository,
        UserAdminGroupRepository $userAdminGroupRepository)
    {
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
        $this->groupRepository = $groupRepository;
        $this->userAdminGroupRepository = $userAdminGroupRepository;
    }

    private function getUserAdminGroup(int $id): UserAdminGroup
    {
        $userAdminGroup = $this->userAdminGroupRepository->find($id);
        if (is_null($userAdminGroup)) {
            throw new Exception('Группа игрока не найдена!');
        }

        return $userAdminGroup;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id, false);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    private function getPrimaryGroups(): array
    {
        return array_map(function (Group $group) {
            return $group->getName();
        }, $this->groupRepository->getAllAdmin(true));
    }

    private function checkUser(User $user, Server $server): bool
    {
        $groups = $user->getAdminGroups()->filter(function (UserAdminGroup $userAdminGroup) use ($server) {
            return $userAdminGroup->getServer() === $server;
        });

        return count($groups) == 0;
    }

    private function transitToMass(User $admin, UserAdminGroup $userAdminGroup): UserAdminGroup
    {
        if (!$admin->permissions()->hasMPPermission(Permissions::MP_TEAM_VIEW_ALL)) {
            throw new PermissionDeniedException();
        }

        if (is_null($userAdminGroup->getServer())) {
            throw new Exception('Этот игрок уже является масс');
        }

        $userAdminGroups = $userAdminGroup->getUser()->getAdminGroups()->filter(function (UserAdminGroup $adminGroup) {
            return $adminGroup->getGroup()->isPrimary();
        });

        foreach ($userAdminGroups as $adminGroup)
        {
            $this->userAdminGroupRepository->delete($adminGroup);
        }

        $userAdminGroup = new UserAdminGroup($userAdminGroup->getUser(), null, $userAdminGroup->getGroup());
        $this->userAdminGroupRepository->create($userAdminGroup);

        $groups = $this->getPrimaryGroups();
        foreach ($this->serverRepository->getAll(false) as $server)
        {
            CabinetUtils::getPermissionsManager($server)
                ->setPrimaryGroup($groups, $userAdminGroup->getUser()->getUuid(), $userAdminGroup->getGroup()->getName());
        }

        if ($userAdminGroup->getGroup()->isPrimary()) {
            if (!is_null($userAdminGroup->getGroup()->getForumId())) {
                $member = ForumManager::getMember($userAdminGroup->getUser());
                if (!is_null($member)) {
                    $member->setGroupId($userAdminGroup->getGroup()->getForumId());
                    $member->setTitle(ucfirst($userAdminGroup->getGroup()->getName()) . ' / Все сервера');

                    ForumManager::updateMember($member);
                }
            }
        }

        return $userAdminGroup;
    }

    private function transitFromMass(User $admin, UserAdminGroup $userAdminGroup, int $serverId): UserAdminGroup
    {
        $server = $this->getServer($serverId);

        if (!$admin->permissions()->hasMPPermission(Permissions::MP_TEAM_VIEW_ALL)) {
            throw new PermissionDeniedException();
        }

        $userAdminGroups = $userAdminGroup->getUser()->getAdminGroups()->filter(function (UserAdminGroup $adminGroup) {
            return $adminGroup->getGroup()->isPrimary();
        });

        foreach ($userAdminGroups as $adminGroup)
        {
            if ($adminGroup->getServer() !== $server) {
                CabinetUtils::getPermissionsManager($server)
                    ->removeGroup($adminGroup->getUser()->getUuid(), $adminGroup->getGroup()->getName());
            }
        }

        $this->userAdminGroupRepository->delete($userAdminGroup);

        $userAdminGroup = new UserAdminGroup($userAdminGroup->getUser(), $server, $userAdminGroup->getGroup());
        $this->userAdminGroupRepository->create($userAdminGroup);

        if ($userAdminGroup->getGroup()->isPrimary()) {
            if (!is_null($userAdminGroup->getGroup()->getForumId())) {
                $member = ForumManager::getMember($userAdminGroup->getUser());
                if (!is_null($member)) {
                    $member->setGroupId($userAdminGroup->getGroup()->getForumId());
                    $member->setTitle(ucfirst($userAdminGroup->getGroup()->getName()) . ' / ' . $server->getName());

                    ForumManager::updateMember($member);
                }
            }
        }

        return $userAdminGroup;
    }

    private function transitStandard(User $admin, UserAdminGroup $userAdminGroup, int $serverId): UserAdminGroup
    {
        $server = $this->getServer($serverId);

        $allowedServers = $admin->permissions()->getServersWithPermission(Permissions::MP_TEAM_TRANSIT);

        if (
            !$admin->permissions()->hasMPPermission(Permissions::MP_TEAM_VIEW_ALL)
            &&
            (
                (!is_null($allowedServers) && !in_array($server, $allowedServers))
                ||
                (!is_null($allowedServers) && !in_array($userAdminGroup->getServer(), $allowedServers))
            )
        ) {
            throw new PermissionDeniedException();
        }

        if ($userAdminGroup->getServer() === $server) {
            throw new Exception('Вы пытаетесь перевести игрока на тот же сервер!');
        }

        if (!$this->checkUser($userAdminGroup->getUser(), $server)) {
            throw new Exception('У этого игрока уже есть группа на этом сервере!');
        }

        CabinetUtils::getPermissionsManager($userAdminGroup->getServer())
            ->removeGroup($userAdminGroup->getUser()->getUuid(), $userAdminGroup->getGroup()->getName());

        CabinetUtils::getPermissionsManager($server)
            ->setPrimaryGroup($this->getPrimaryGroups(), $userAdminGroup->getUser()->getUuid(), $userAdminGroup->getGroup()->getName());

        $this->userAdminGroupRepository->delete($userAdminGroup);

        $userAdminGroup = new UserAdminGroup(
            $userAdminGroup->getUser(),
            $server,
            $userAdminGroup->getGroup()
        );
        $this->userAdminGroupRepository->create($userAdminGroup);

        if ($userAdminGroup->getGroup()->isPrimary()) {
            if (!is_null($userAdminGroup->getGroup()->getForumId())) {
                $member = ForumManager::getMember($userAdminGroup->getUser());
                if (!is_null($member)) {
                    $member->setGroupId($userAdminGroup->getGroup()->getForumId());
                    $member->setTitle(ucfirst($userAdminGroup->getGroup()->getName()) . ' / ' . $server->getName());

                    ForumManager::updateMember($member);
                }
            }
        }

        return $userAdminGroup;
    }

    public function handle(User $admin, int $id, int $serverId): void
    {
        $userAdminGroup = $this->getUserAdminGroup($id);
        if ($admin === $userAdminGroup->getUser()) {
            throw new Exception('Вы не можете перевести себя!');
        }

        if (!in_array(
            $userAdminGroup->getGroup(),
            GroupsHelper::getAllowedManageGroups($admin, $userAdminGroup->getServer(), $this->groupRepository->getAllAdmin())
        )) {
            throw new Exception('Вы не можете перевести игрока с этой группой!');
        }
        $old = $userAdminGroup;

        if ($serverId == 0) {
            $userAdminGroup = $this->transitToMass($admin, $userAdminGroup);
        } elseif (is_null($userAdminGroup->getServer())) {
            $userAdminGroup = $this->transitFromMass($admin, $userAdminGroup, $serverId);
        } else {
            $userAdminGroup = $this->transitStandard($admin, $userAdminGroup, $serverId);
        }

        event(new TransitTeamEvent($admin, $old, $userAdminGroup));
    }
}