<?php


namespace App\Events\Admin\SitePerms;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class AddPermissionsEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Group[]
     */
    private $groups;

    /**
     * @var String[]
     */
    private $permissions;

    /**
     * AddPermissionsEvent constructor.
     * @param User $admin
     * @param Group[] $groups
     * @param String[] $permissions
     */
    public function __construct(User $admin, array $groups, array $permissions)
    {
        $this->admin = $admin;
        $this->groups = $groups;
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
     * @return Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
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
            'groups' => array_map(function (Group $group) {
                return $group->toArray();
            }, $this->groups),
            'permissions' => $this->permissions
        ];
    }
}