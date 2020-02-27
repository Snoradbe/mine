<?php


namespace App\Services\Cabinet\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
use App\Exceptions\Exception;

class AdminPrimaryGroup extends PrimaryGroup
{
    /*
     * @Override
     */
    private function getPrimaryGroups(): array
    {
        if (empty($this->primaryGroups)) {
            $groups = $this->groupRepository->getAll();
            /* @var Group $group */
            foreach ($groups as $group)
            {
                if ($group->isPrimary() && $group->isAdmin()) {
                    $this->primaryGroups[] = $group->getName();
                }
            }
        }

        return $this->primaryGroups;
    }

    /*
     * @Override
     */
    public function give(User $user, Server $server, Group $group, int $days, bool $adminMode = false)
    {
        $primary = $user->getAdminGroups()->filter(function (UserAdminGroup $userGroup) use ($server) {
            return $userGroup->getServer()->getId() == $server->getId()
                && $userGroup->getGroup()->isPrimary() && $userGroup->getGroup()->isAdmin();
        })->first();

        /*
         * Если есть группа,
         * то продлеваем ее или изменяем
         */
        if ($primary instanceof UserAdminGroup) {

            if ($primary->getGroup()->getId() == $group->getId()) {
                return $primary;
            } else {
                //изменяем группу
                $ug = new UserAdminGroup($user, $server, $group);
                $user->getAdminGroups()->removeElement($primary);
                $user->getAdminGroups()->add($ug);
                $this->setPrimaryGroup($server, $user->getUuid(), $group->getName());

                return $ug;
            }
        }

        /*
         * Если вообще нет групп,
         * то выдаем
         */
        $newGroup = new UserAdminGroup($user, $server, $group);

        $user->getAdminGroups()->add($newGroup);
        $this->setPrimaryGroup($server, $user->getUuid(), $group->getName());

        return $newGroup;
    }

    public function take(User $user, Server $server, Group $group): void
    {
        $userGroup = $user->getAdminGroups()->filter(function (UserAdminGroup $userGroup) use ($server, $group) {
            return $userGroup->getServer() === $server && $userGroup->getGroup() === $group;
        })->first();

        if (!($userGroup instanceof UserAdminGroup)) {
            throw new Exception('Группа не найдена!');
        }

        $user->getAdminGroups()->removeElement($userGroup);
        $this->removeGroup($server, $user->getUuid(), $group->getName());
    }
}