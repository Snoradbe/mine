<?php


namespace App\Handlers\Admin\Cabinet;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserGroup;
use App\Events\Admin\Cabinet\GiveGroupEvent;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\User\UserRepository;
use App\Repository\Site\UserGroup\UserGroupRepository;
use App\Services\Cabinet\CabinetUtils;
use App\Services\Permissions\Permissions;

class GiveGroupHandler
{
    private $userRepository;

    private $serverRepository;

    private $groupRepository;

    private $userGroupRepository;

    public function __construct(
        UserRepository $userRepository,
        ServerRepository $serverRepository,
        GroupRepository $groupRepository,
        UserGroupRepository $userGroupRepository)
    {
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
        $this->groupRepository = $groupRepository;
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

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    private function getGroup(int $id): Group
    {
        $group = $this->groupRepository->find($id);
        if (is_null($group)) {
            throw new Exception('Группа не найдена!');
        }

        return $group;
    }

    private function getDonateGroups(): array
    {
        return array_map(function (Group $group) {
            return $group->getName();
        }, $this->groupRepository->getAllDonate(true));
    }

    public function handle(User $admin, int $userId, int $serverId, int $groupId, ?string $date): void
    {
        $target = $this->getUser($userId);
        $server = $this->getServer($serverId);
        $group = $this->getGroup($groupId);

        if (
            !$admin->permissions()->hasMPPermission(Permissions::MP_CABINET_VIEW_ALL)
            &&
            !is_null($admin->permissions()->getServersWithPermission(Permissions::MP_CABINET_VIEW))
            &&
            !in_array($server, $admin->permissions()->getServersWithPermission(Permissions::MP_CABINET_VIEW))
        ) {
            throw new PermissionDeniedException();
        }

        if (!empty($date)) {
            try {
                $expire = (new \DateTimeImmutable($date))->getTimestamp();
            } catch (\Exception $exception) {
                throw new Exception('Неправильный формат даты!');
            }
        } else {
            $expire = 0;
        }

        if (!empty($date) && $expire <= time()) {
            throw new Exception('Дата должна быть больше чем сейчас!');
        }

        $userGroups = $target->getGroups()->filter(function (UserGroup $uGroup) use ($server) {
            return $uGroup->getServer() === $server;
        });

        if (count($userGroups) > 0) {
            /* @var UserGroup $similarUserGroup */
            $similarUserGroup = $userGroups->filter(function (UserGroup $uGroup) use ($group) {
                return $uGroup->getGroup() === $group;
            })->first();

            if ($similarUserGroup instanceof UserGroup) {
                $similarUserGroup->setExpireAt($expire);
                $this->userGroupRepository->update($similarUserGroup);
                event(new GiveGroupEvent($admin, $target, $server, $group, $similarUserGroup->getExpireAt()));
                return;
            }

            if ($group->isPrimary()) {
                $primary = $userGroups->filter(function (UserGroup $uGroup) {
                    return $uGroup->getGroup()->isPrimary();
                })->first();

                if ($primary instanceof UserGroup) {
                    CabinetUtils::getPermissionsManager($server)
                        ->removeGroup($target->getUuid(), $primary->getGroup()->getName());
                    $this->userGroupRepository->delete($primary);
                }
            }
        }

        $userGroup = new UserGroup($target, $server, $group, $expire);
        CabinetUtils::getPermissionsManager($server)
            ->setPrimaryGroup($this->getDonateGroups(), $target->getUuid(), $group->getName());
        $this->userGroupRepository->create($userGroup);
        event(new GiveGroupEvent($admin, $target, $server, $group, $userGroup->getExpireAt()));
    }
}