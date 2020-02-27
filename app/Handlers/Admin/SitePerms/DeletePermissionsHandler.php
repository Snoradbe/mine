<?php


namespace App\Handlers\Admin\SitePerms;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\SitePerms\DeletePermissionsEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;
use App\Services\Cabinet\CabinetUtils;

class DeletePermissionsHandler
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

    public function handle(User $admin, string $group, array $permissions): void
    {
        if ($group == 'default') {
            $group = CabinetUtils::getDefaultGroup();
        } else {
            $group = $this->getGroup($group);
        }

        foreach ($permissions as $permission)
        {
            $group->removePermission($permission);
        }

        if ($group->getName() == 'default') {
            CabinetUtils::saveDefaultGroup($group);
        } else {
            $this->groupRepository->update($group);
        }

        event(new DeletePermissionsEvent($admin, $group, $permissions));
    }
}