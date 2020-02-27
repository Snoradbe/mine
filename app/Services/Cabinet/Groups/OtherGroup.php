<?php


namespace App\Services\Cabinet\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserGroup;
use App\Exceptions\Exception;
use App\Services\Game\Permissions\PermissionsManager;

class OtherGroup
{
    private function getConfig(Server $server): array
    {
        return config(
            'site.game.permissions.' . $server->getId(),
            config('site.game.permissions.default')
        );
    }

    private function addPermission(Server $server, string $uuid, string $permission): void
    {
        $config = $this->getConfig($server);

        /* @var \App\Services\Game\Permissions\PermissionsManager $manager */
        $manager = new $config['manager']($config, $server->getConnectionName());
        $manager->addPermission($uuid, $permission);
    }

    private function removePermission(Server $server, string $uuid, string $permission): void
    {
        $config = $this->getConfig($server);

        /* @var \App\Services\Game\Permissions\PermissionsManager $manager */
        $manager = new $config['manager']($config, $server->getConnectionName());
        $manager->removePermission($uuid, $permission);
    }

    private function createExpire(int $days, ?int $currentExpire = null): int
    {
        return $days == -1 ? 0 : ((is_null($currentExpire) ? time() : $currentExpire) + ($days * 86400));
    }

    public function give(User $user, ?Server $server, Group $group, int $days, bool $adminMode = false): UserGroup
    {
        $other = $user->getGroups()->filter(function (UserGroup $userGroup) use ($server, $group) {
            return $userGroup->getServer()->getId() == $server->getId()
                && $userGroup->getGroup()->getId() == $group->getId();
        })->first();

        /*
         * Если есть группа,
         * то продлеваем ее
         */
        if ($other instanceof UserGroup) {
            //продлеваем
            if (!$adminMode && $other->getExpireAt() == 0) {
                throw new Exception('Эту группу уже некуда продлевать!');
            }

            $other->setExpireAt($this->createExpire($days, $other->getExpireAt()));

            return $other;
        }

        /*
         * Иначе, выдаем
         */
        $newGroup = new UserGroup($user, $server, $group, $this->createExpire($days));

        $user->getGroups()->add($newGroup);
        $this->addPermission($server, $user->getUuid(), $group->getPermissionName());

        return $newGroup;
    }

    public function take(User $user, ?Server $server, Group $group): void
    {
        $userGroup = $user->getGroups()->filter(function (UserGroup $userGroup) use ($server, $group) {
            return $userGroup->getServer() === $server && $userGroup->getGroup() === $group;
        })->first();

        if (!($userGroup instanceof UserGroup)) {
            throw new Exception('Группа не найдена!');
        }

        $user->getGroups()->removeElement($userGroup);
        $this->removePermission($server, $user->getUuid(), $group->getName());
    }
}