<?php


namespace App\Services\Cabinet\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserGroup;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;

class PrimaryGroup
{
    protected $groupRepository;

    protected $primaryGroups = [];

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    private function getPrimaryGroups(): array
    {
        if (empty($this->primaryGroups)) {
            $groups = $this->groupRepository->getAll();
            /* @var Group $group */
            foreach ($groups as $group)
            {
                if ($group->isPrimary() && !$group->isAdmin()) {
                    $this->primaryGroups[] = $group->getName();
                }
            }
        }

        return $this->primaryGroups;
    }

    private function getConfig(Server $server): array
    {
        return config(
            'site.game.permissions.' . $server->getId(),
            config('site.game.permissions.default')
        );
    }

    private function getRepositoryClass(Server $server): string
    {
        return config(
            'site.game.permissions.' . $server->getId(),
            config('site.game.permissions.default')
        );
    }

    protected function setPrimaryGroup(Server $server, string $uuid, string $group): void
    {
        $config = $this->getConfig($server);

        /* @var \App\Services\Game\Permissions\PermissionsManager $manager */
        $manager = new $config['manager']($config, $server->getConnectionName());
        $manager->setPrimaryGroup($this->getPrimaryGroups(), $uuid, $group);
    }

    protected function removeGroup(Server $server, string $uuid, string $group): void
    {
        $config = $this->getConfig($server);

        /* @var \App\Services\Game\Permissions\PermissionsManager $manager */
        $manager = new $config['manager']($config, $server->getConnectionName());
        $manager->removeGroup($uuid, $group);
    }

    protected function createExpire(int $days, ?int $currentExpire = null): int
    {
        return $days == -1 ? 0 : ((is_null($currentExpire) ? time() : $currentExpire) + ($days * 86400));
    }


    public function give(User $user, Server $server, Group $group, int $days, bool $adminMode = false): UserGroup
    {
        $primary = $user->getGroups()->filter(function (UserGroup $userGroup) use ($server) {
            return $userGroup->getServer()->getId() == $server->getId()
                && $userGroup->getGroup()->isPrimary() && !$userGroup->getGroup()->isAdmin();
        })->first();

        /*
         * Если есть группа,
         * то продлеваем ее или изменяем
         */
        if ($primary instanceof UserGroup) {
            if (!$adminMode && $primary->getGroup()->getWeight() > $group->getWeight()) {
                throw new Exception('Текущая группа лучше выбранной!');
            }

            if ($primary->getGroup()->getId() == $group->getId()) {
                //продлеваем
                if (!$adminMode && $primary->getExpireAt() == 0) {
                    throw new Exception('Эту группу уже некуда продлевать!');
                }

                $primary->setExpireAt($this->createExpire($days, $primary->getExpireAt()));

                return $primary;
            } else {
                //изменяем группу
                $ug = new UserGroup($user, $server, $group, $this->createExpire($days));
                $user->getGroups()->removeElement($primary);
                $user->getGroups()->add($ug);
                $this->setPrimaryGroup($server, $user->getUuid(), $group->getName());

                return $ug;
            }
        }

        /*
         * Если вообще нет групп,
         * то выдаем
         */
        $newGroup = new UserGroup($user, $server, $group, $this->createExpire($days));

        $user->getGroups()->add($newGroup);
        $this->setPrimaryGroup($server, $user->getUuid(), $group->getName());

        return $newGroup;
    }

    public function take(User $user, Server $server, Group $group): void
    {
        $userGroup = $user->getGroups()->filter(function (UserGroup $userGroup) use ($server, $group) {
            return $userGroup->getServer() === $server && $userGroup->getGroup() === $group;
        })->first();

        if (!($userGroup instanceof UserGroup)) {
            throw new Exception('Группа не найдена!');
        }

        $user->getGroups()->removeElement($userGroup);
        $this->removeGroup($server, $user->getUuid(), $group->getName());
    }
}