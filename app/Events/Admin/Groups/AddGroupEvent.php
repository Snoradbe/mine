<?php


namespace App\Events\Admin\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class AddGroupEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Group
     */
    private $group;

    /**
     * AddGroupEvent constructor.
     * @param User $admin
     * @param Group $group
     */
    public function __construct(User $admin, Group $group)
    {
        $this->admin = $admin;
        $this->group = $group;
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
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'group' => $this->group->toArray()
        ];
    }
}