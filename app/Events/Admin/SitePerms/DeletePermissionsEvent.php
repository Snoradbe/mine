<?php


namespace App\Events\Admin\SitePerms;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class DeletePermissionsEvent implements Event
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
     * @var String[]
     */
    private $permissions;

    /**
     * AddPermissionsEvent constructor.
     * @param User $admin
     * @param Group $group
     * @param String[] $permissions
     */
    public function __construct(User $admin, Group $group, array $permissions)
    {
        $this->admin = $admin;
        $this->group = $group;
        $this->permissions = $permissions;
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
     * @return String[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'group' => $this->group->toArray(),
            'permissions' => $this->permissions
        ];
    }
}