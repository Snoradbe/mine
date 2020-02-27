<?php


namespace App\Handlers\Admin\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\Groups\AddGroupEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;

class AddHandler
{
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
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
        ?int $parent,
        string $name,
        int $weight,
        bool $isPrimary,
        bool $isAdmin,
        ?string $permissionName = null,
        ?int $forumId = null): Group
    {
        $parentGroup = null;
        if (!is_null($parent)) {
            $parentGroup = $this->getParent($parent);
        }

        $group = new Group(
            $name,
            $isPrimary ? $weight : 0,
            $isPrimary,
            $isAdmin,
            $isPrimary ? null : $permissionName,
            $forumId
        );

        $group->setParent($parentGroup);

        $this->groupRepository->create($group);

        event(new AddGroupEvent($admin, $group));

        return $group;
    }
}