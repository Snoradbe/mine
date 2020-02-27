<?php


namespace App\Handlers\Admin\AdminPerms;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\AdminPerms\AddPermissionsEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;

class AddPermissionsHandler
{
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    private function getGroup(string $name): Group
    {
        $group = $this->groupRepository->findByName($name);
        if (is_null($group)) {
            throw new Exception('Группа не найдена!');
        }

        return $group;
    }

    public function handle(User $admin, array $groups, array $permissions): void
    {
        $resultGroups = [];

        foreach ($groups as $group)
        {
            $group = $this->getGroup($group);
            foreach ($permissions as $permission)
            {
                $group->addPermission($permission);
            }

            $this->groupRepository->update($group);

            $resultGroups[] = $group;
        }

        event(new AddPermissionsEvent($admin, $resultGroups, $permissions));
    }
}