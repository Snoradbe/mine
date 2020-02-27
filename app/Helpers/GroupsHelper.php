<?php


namespace App\Helpers;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
use App\Services\Permissions\Permissions;

class GroupsHelper
{
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}

    /**
     * Получаем все родительские группы
     *
     * @param Group $group
     * @param array $groups
     */
    public static function walkParent(Group $group, array &$groups): void
    {
        if (!in_array($group, $groups)) {
            $groups[] = $group;
        }
        if (!is_null($group->getParent())) {
            self::walkParent($group->getParent(), $groups);
        }
    }

    /**
     * Получаем все группы, которые может выдать игрок
     *
     * @param User $admin
     * @param Server $server
     * @param Group[] $allGroups
     * @return Group[]
     */
    public static function getAllowedManageGroups(User $admin, ?Server $server, array $allGroups): array
    {
        if ($admin->permissions()->hasMPPermission(Permissions::MP_TEAM_ALL_GROUPS)) {
            $uWeight = 0;
            /* @var UserAdminGroup $userAdminGroup */
            foreach ($admin->getAdminGroups() as $userAdminGroup)
            {
                if (!is_null($server) && !is_null($userAdminGroup->getServer()) && $userAdminGroup !== $server) {
                    continue;
                }

                if ($uWeight < $userAdminGroup->getGroup()->getWeight()) {
                    $uWeight = $userAdminGroup->getGroup()->getWeight();
                }
            }

            return array_filter($allGroups, function (Group $group) use ($uWeight) {
                return $group->getWeight() < $uWeight;
            });
        }

        $groups = [];
        /* @var UserAdminGroup $userAdminGroup */
        foreach ($admin->getAdminGroups() as $userAdminGroup)
        {
            if (!is_null($server) && !is_null($userAdminGroup->getServer()) && $userAdminGroup->getServer() !== $server) {
                continue;
            }

            //только те группы, которые наследуются от другой
            if (!is_null($userAdminGroup->getGroup()->getParent())) {
                self::walkParent($userAdminGroup->getGroup()->getParent(), $groups);
            }
        }

        return $groups;
    }
}