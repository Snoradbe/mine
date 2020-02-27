<?php


namespace App\Handlers\Admin\Team;


use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
use App\Events\Admin\Team\DeleteFromTeamEvent;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Helpers\GroupsHelper;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\UserAdminGroup\UserAdminGroupRepository;
use App\Services\Cabinet\CabinetUtils;
use App\Services\Forum\ForumManager;
use App\Services\Permissions\Permissions;

class DeleteHandler
{
    private $userAdminGroupRepository;

    private $groupRepository;

    private $serverRepository;

    public function __construct(UserAdminGroupRepository $userAdminGroupRepository, GroupRepository $groupRepository, ServerRepository $serverRepository)
    {
        $this->userAdminGroupRepository = $userAdminGroupRepository;
        $this->groupRepository = $groupRepository;
        $this->serverRepository = $serverRepository;
    }

    private function getUserAdminGroup(int $id): UserAdminGroup
    {
        $userAdminGroup = $this->userAdminGroupRepository->find($id);
        if (is_null($userAdminGroup)) {
            throw new Exception('Группа игрока не найдена!');
        }

        return $userAdminGroup;
    }

    public function handle(User $admin, int $id): void
    {
        $userAdminGroup = $this->getUserAdminGroup($id);
        if ($admin === $userAdminGroup->getUser()) {
            throw new Exception('Вы не можете разжаловать себя!');
        }

        $allowedServers = $admin->permissions()->getServersWithPermission(Permissions::MP_TEAM_REMOVE);

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

        if (!in_array(
            $userAdminGroup->getGroup(),
            GroupsHelper::getAllowedManageGroups($admin, $userAdminGroup->getServer(), $this->groupRepository->getAllAdmin())
        )) {
            throw new Exception('Вы не можете снять игрока с этой группы!');
        }

        if (is_null($userAdminGroup->getServer())) {
            foreach ($this->serverRepository->getAll(false) as $server)
            {
                CabinetUtils::getPermissionsManager($server)
                    ->removeGroup($userAdminGroup->getUser()->getUuid(), $userAdminGroup->getGroup()->getName());
            }
        } else {
            CabinetUtils::getPermissionsManager($userAdminGroup->getServer())
                ->removeGroup($userAdminGroup->getUser()->getUuid(), $userAdminGroup->getGroup()->getName());
        }

        $count = count($userAdminGroup->getUser()->getAdminGroups()->filter(function (UserAdminGroup $uaGroup) use ($userAdminGroup) {
            return $uaGroup->getGroup()->isPrimary() && $uaGroup !== $userAdminGroup;
        }));

        $this->userAdminGroupRepository->delete($userAdminGroup);

        if ($userAdminGroup->getGroup()->isPrimary()) {
            if ($count < 1) {
                $member = ForumManager::getMember($userAdminGroup->getUser());
                if (!is_null($member)) {
                    $member->setGroupId(config('site.forum.default_group', 666));
                    $member->setTitle(null);

                    ForumManager::updateMember($member);
                }
            }
        }

        event(new DeleteFromTeamEvent($admin, $userAdminGroup));
    }
}