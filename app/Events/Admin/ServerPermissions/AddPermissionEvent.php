<?php


namespace App\Events\Admin\ServerPermissions;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class AddPermissionEvent implements Event
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
     * @var array
     */
    private $groups;

    /**
     * @var array
     */
    private $permissions;

    /**
     * AddPermissionHandler constructor.
     * @param User $admin
     * @param Server|null $server
     * @param array $groups
     * @param array $permissions
     */
    public function __construct(User $admin, ?Server $server, array $groups, array $permissions)
    {
        $this->admin = $admin;
        $this->server = $server;
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
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->server;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @return array
     */
    public function getPermission(): array
    {
        return $this->permissions;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'groups' => $this->groups,
            'permissions' => $this->permissions
        ];
    }
}