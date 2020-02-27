<?php


namespace App\Events\Admin\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class EditGroupEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Group
     */
    private $oldGroup;

    /**
     * @var Group
     */
    private $newGroup;

    /**
     * EditGroupEvent constructor.
     * @param User $admin
     * @param Group $oldGroup
     * @param Group $newGroup
     */
    public function __construct(User $admin, Group $oldGroup, Group $newGroup)
    {
        $this->admin = $admin;
        $this->oldGroup = $oldGroup;
        $this->newGroup = $newGroup;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Group
     */
    public function getOldGroup(): Group
    {
        return $this->oldGroup;
    }

    /**
     * @return Group
     */
    public function getNewGroup(): Group
    {
        return $this->newGroup;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'old' => $this->oldGroup->toArray(),
            'new' => $this->newGroup->toArray()
        ];
    }
}