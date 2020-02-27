<?php


namespace App\Handlers\Admin\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\Groups\EditGroupEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;

class EditHandler
{
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    private function getGroup(int $id): Group
    {
        $group = $this->groupRepository->find($id);
        if (is_null($group)) {
            throw new Exception('Группа не найдена!');
        }

        return $group;
    }

    private function getParent(int $id): Group
    {
        $group = $this->groupRepository->find($id);
        if (is_null($group)) {
            throw new Exception('Родительская группа не найдена!');
        }

        return $group;
    }

    public function handle(
        User $admin,
        int $id,
        ?int $parent,
        string $name,
        int $weight,
        bool $isPrimary,
        bool $isAdmin,
        ?string $permissionName = null,
        ?int $forumId = null): void
    {
        $parentGroup = null;
        if (!is_null($parent)) {
            $parentGroup = $this->getParent($parent);
        }

        $group = $this->getGroup($id);
        if ($group === $parentGroup) {
            throw new Exception('Группа не может наследовать сама себя!');
        }
        $old = clone $group;

        $group->setParent($parentGroup);
        $group->setName($name);
        $group->setWeight($isPrimary ? $weight : 0);
        $group->setIsPrimary($isPrimary);
        $group->setIsAdmin($isAdmin);
        $group->setPermissionName($isPrimary ? null : $permissionName);
        $group->setForumId($forumId);

        $this->groupRepository->update($group);

        event(new EditGroupEvent($admin, $old, $group));
    }
}