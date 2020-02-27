<?php


namespace App\Handlers\Admin\Team;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
use App\Events\Admin\Team\AddToTeamEvent;
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

class AddHandler
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

    private function getUser(string $name): User
    {
        $user = $this->userRepository->findByName($name);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id, false);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    private function getGroup(int $id): Group
    {
        $group = $this->groupRepository->find($id);
        if (is_null($group) || !$group->isAdmin()) {
            throw new Exception('Группа не найдена!');
        }

        return $group;
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
            return is_null($userAdminGroup->getServer()) || $userAdminGroup->getServer() === $server;
        });

        return count($groups) == 0;
    }

    private function handleOne(User $admin, User $target, Group $group, int $serverId): Server
    {
        $server = $this->getServer($serverId);

        if (!in_array($group, GroupsHelper::getAllowedManageGroups($admin, $server, $this->groupRepository->getAllAdmin()))) {
            throw new Exception('Вы не можете принять игрока в эту группу!');
        }

        $allowedServers = $admin->permissions()->getServersWithPermission(Permissions::MP_TEAM_ADD);

        if (
            !$admin->permissions()->hasMPPermission(Permissions::MP_TEAM_VIEW_ALL)
            &&
            !is_null($allowedServers)
            &&
            !in_array($server, $allowedServers)
        ) {
            throw new PermissionDeniedException();
        }

        if (!$this->checkUser($target, $server)) {
            throw new Exception('У этого игрока уже есть группа на этом сервере!');
        }

        CabinetUtils::getPermissionsManager($server)
            ->setPrimaryGroup($this->getPrimaryGroups(), $target->getUuid(), $group->getName());

        if ($group->isPrimary()) {
            if (!is_null($group->getForumId())) {
                $member = ForumManager::getMember($target);
                if (!is_null($member)) {
                    $member->setGroupId($group->getForumId());
                    $member->setTitle(ucfirst($group->getName()) . ' / ' . $server->getName());

                    ForumManager::updateMember($member);
                }
            }
        }

        return $server;
    }

    private function handleMass(User $admin, User $target, Group $group): void
    {
        if (!$admin->permissions()->hasMPPermission(Permissions::MP_TEAM_VIEW_ALL)) {
            throw new PermissionDeniedException();
        }

        if (!in_array($group, GroupsHelper::getAllowedManageGroups($admin, null, $this->groupRepository->getAllAdmin()))) {
            throw new Exception('Вы не можете принять игрока в эту группу!');
        }

        if ($target->inTeam()) {
            throw new Exception('Этот игрок уже является частью администрации. Сначала нужно удалить прежнюю группу!');
        }

        $groups = $this->getPrimaryGroups();
        foreach ($this->serverRepository->getAll(false) as $server)
        {
            CabinetUtils::getPermissionsManager($server)
                ->setPrimaryGroup($groups, $target->getUuid(), $group->getName());
        }

        if ($group->isPrimary()) {
            if (!is_null($group->getForumId())) {
                $member = ForumManager::getMember($target);
                if (!is_null($member)) {
                    $member->setGroupId($group->getForumId());
                    $member->setTitle(ucfirst($group->getName()) . ' / Все сервера');

                    ForumManager::updateMember($member);
                }
            }
        }
    }

    public function handle(User $admin, string $name, int $serverId, int $groupId): void
    {
        $target = $this->getUser($name);
        if ($target === $admin) {
            throw new Exception('Вы не можете принять самого себя!');
        }
        $group = $this->getGroup($groupId);

        if ($serverId != 0) {
            $server = $this->handleOne($admin, $target, $group, $serverId);
        } else {
            $this->handleMass($admin, $target, $group);
            $server = null;
        }

        $userAdminGroup = new UserAdminGroup($target, $server, $group);
        $this->userAdminGroupRepository->create($userAdminGroup);

        event(new AddToTeamEvent($admin, $server, $target, $group));
    }
}