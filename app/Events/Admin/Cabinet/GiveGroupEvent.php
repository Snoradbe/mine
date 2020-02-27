<?php


namespace App\Events\Admin\Cabinet;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class GiveGroupEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var User
     */
    private $target;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var Group
     */
    private $group;

    /**
     * @var int
     */
    private $expire;

    /**
     * GiveGroupEvent constructor.
     * @param User $admin
     * @param User $target
     * @param Server $server
     * @param Group $group
     * @param int $expire
     */
    public function __construct(User $admin, User $target, Server $server, Group $group, int $expire)
    {
        $this->admin = $admin;
        $this->target = $target;
        $this->server = $server;
        $this->group = $group;
        $this->expire = $expire;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return User
     */
    public function getTarget(): User
    {
        return $this->target;
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
     * @return int
     */
    public function getExpire(): int
    {
        return $this->expire;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'target' => [
                'id' => $this->target->getId(),
                'name' => $this->target->getName()
            ],
            'group' => $this->group->toArray(),
            'expire' => $this->expire
        ];
    }
}