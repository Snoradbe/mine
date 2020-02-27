<?php


namespace App\Events\Admin\ServerPermissions;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class DeletePermissionEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Server|null
     */
    private $server;

    /**
     * @var Group
     */
    private $group;

    /**
     * @var array
     */
    private $permissions;

    /**
     * DeletePermissionEvent constructor.
     * @param User $admin
     * @param Server|null $server
     * @param Group $group
     * @param array $permissions
     */
    public function __construct(User $admin, ?Server $server, Group $group, array $permissions)
    {
        $this->admin = $admin;
        $this->server = $server;
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
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->server;
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