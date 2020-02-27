<?php


namespace App\Events\Admin\Cabinet\Settings\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class RemoveGroupEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var Group
     */
    private $group;

    /**
     * @var array
     */
    private $periods;

    /**
     * RemoveGroupEvent constructor.
     * @param User $admin
     * @param Server $server
     * @param Group $group
     * @param array $periods
     */
    public function __construct(User $admin, Server $server, Group $group, array $periods)
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->group = $group;
        $this->periods = $periods;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
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
    public function getPeriods(): array
    {
        return $this->periods;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'group' => $this->group->toArray(),
            'periods' => $this->periods
        ];
    }
}