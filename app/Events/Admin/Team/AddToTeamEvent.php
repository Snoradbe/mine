<?php


namespace App\Events\Admin\Team;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class AddToTeamEvent implements Event
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
     * @var User
     */
    private $target;

    /**
     * @var Group
     */
    private $group;

    /**
     * AddToTeamEvent constructor.
     * @param User $admin
     * @param Server|null $server
     * @param User $target
     * @param Group $group
     */
    public function __construct(User $admin, ?Server $server, User $target, Group $group)
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->target = $target;
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
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->server;
    }

    /**
     * @return User
     */
    public function getTarget(): User
    {
        return $this->target;
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
            'target' => [
                'id' => $this->target->getId(),
                'name' => $this->target->getName()
            ],
            'group' => $this->group->toArray()
        ];
    }
}