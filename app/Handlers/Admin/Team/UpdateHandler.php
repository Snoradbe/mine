<?php


namespace App\Handlers\Admin\Team;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
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

class UpdateHandler
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

    private function checkUser(User $user, ?Server $server, Group $group): bool
    {
        return !$user->inAdminGroup($server, $group->getName());
    }

    public function handle(User $admin, int $userGroupId, int $groupId): void
    {
        $userAdminGroup = $this->getUserAdminGroup($userGroupId);
        if ($admin === $userAdminGroup->getUser()) {
            throw new Exception('Вы не можете именить группу себе!');
        }

        $groups = $this->groupRepository->getAllAdmin();
        if (!in_array($userAdminGroup->getGroup(), GroupsHelper::getAllowedManageGroups($admin, null, $groups))) {
            throw new Exception('Вы не можете управлять игроком с такой группой!');
        }
        $group = $this->getGroup($groupId);
        if (!in_array($group, GroupsHelper::getAllowedManageGroups($admin, null, $groups))) {
            throw new Exception('Вы не можете переводить игрока на эту группу!');
        }

        $allowedServers = $admin->permissions()->getServersWithPermission(Permissions::MP_TEAM_UPGRADE);

        if (
            !$admin->permissions()->hasMPPermission(Permissions::MP_TEAM_VIEW_ALL)
            &&
            (
                (is_null($allowedServers) && is_null($userAdminGroup->getServer()))
                ||
                (!is_null($allowedServers) && is_null($userAdminGroup->getServer()))
                ||
                (!is_null($allowedServers) && !in_array($userAdminGroup->getServer(), $allowedServers))
            )
        ) {
            throw new PermissionDeniedException();
        }

        if (!$this->checkUser($userAdminGroup->getUser(), $userAdminGroup->getServer(), $group)) {
            throw new Exception('Вы пытаетесь выдать ту же группу, которая сейчас у игрока!');
        }

        CabinetUtils::getPermissionsManager($userAdminGroup->getServer())
            ->setPrimaryGroup($this->getPrimaryGroups(), $userAdminGroup->getUser()->getUuid(), $userAdminGroup->getGroup()->getName());

        $this->userAdminGroupRepository->delete($userAdminGroup);

        $userAdminGroup = new UserAdminGroup($userAdminGroup->getUser(), $userAdminGroup->getServer(), $group);

        if ($userAdminGroup->getGroup()->isPrimary()) {
            if (!is_null($userAdminGroup->getGroup()->getForumId())) {
                $member = ForumManager::getMember($userAdminGroup->getUser());
                if (!is_null($member)) {
                    $member->setGroupId($userAdminGroup->getGroup()->getForumId());
                    $member->setTitle(ucfirst($userAdminGroup->getGroup()->getName()) . ' / ' . $userAdminGroup->getServer()->getName());

                    ForumManager::updateMember($member);
                }
            }
        }

        $this->userAdminGroupRepository->create($userAdminGroup);
    }
}