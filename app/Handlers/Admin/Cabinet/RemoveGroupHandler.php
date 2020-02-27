<?php


namespace App\Handlers\Admin\Cabinet;


use App\Entity\Site\User;
use App\Entity\Site\UserGroup;
use App\Events\Admin\Cabinet\RemoveGroupEvent;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Repository\Site\User\UserRepository;
use App\Repository\Site\UserGroup\UserGroupRepository;
use App\Services\Cabinet\CabinetUtils;
use App\Services\Permissions\Permissions;

class RemoveGroupHandler
{
    private $userRepository;

    private $userGroupRepository;

    public function __construct(
        UserRepository $userRepository,
        UserGroupRepository $userGroupRepository)
    {
        $this->userRepository = $userRepository;
        $this->userGroupRepository = $userGroupRepository;
    }

    private function getUser(int $id): User
    {
        $user = $this->userRepository->find($id);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    private function getUserGroup(int $id): UserGroup
    {
        $group = $this->userGroupRepository->find($id);
        if (is_null($group)) {
            throw new Exception('Группа игрока не найдена!');
        }

        return $group;
    }

    public function handle(User $admin, int $userId, int $userGroupId): void
    {
        $target = $this->getUser($userId);
        $userGroup = $this->getUserGroup($userGroupId);

        if (
            !$admin->permissions()->hasMPPermission(Permissions::MP_CABINET_VIEW_ALL)
            &&
            !is_null($admin->permissions()->getServersWithPermission(Permissions::MP_CABINET_VIEW))
            &&
            !in_array($userGroup->getServer(), $admin->permissions()->getServersWithPermission(Permissions::MP_CABINET_VIEW))
        ) {
            throw new PermissionDeniedException();
        }

        if ($userGroup->getUser() !== $target) {
            throw new Exception('Эта группа не принадлежит этому игроку!');
        }

        CabinetUtils::getPermissionsManager($userGroup->getServer())
            ->removeGroup($target->getUuid(), $userGroup->getGroup()->getName());
        $this->userGroupRepository->delete($userGroup);

        event(new RemoveGroupEvent($admin, $target, $userGroup));
    }
}