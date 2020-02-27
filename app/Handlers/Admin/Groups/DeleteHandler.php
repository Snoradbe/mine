<?php


namespace App\Handlers\Admin\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\Groups\DeleteGroupEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;

class DeleteHandler
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

    public function handle(User $admin, int $id): void
    {
        $group = $this->getGroup($id);

        $this->groupRepository->delete($group);

        event(new DeleteGroupEvent($admin, $group));
    }
}