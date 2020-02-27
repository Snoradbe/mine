<?php


namespace App\Events\Admin\Cabinet\Settings\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
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
    private $price;

    /**
     * AddGroupEvent constructor.
     * @param User $admin
     * @param Server $server
     * @param Group $group
     * @param int $price
     */
    public function __construct(User $admin, Server $server, Group $group, int $price)
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->group = $group;
        $this->price = $price;
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
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'group' => $this->group->toArray(),
            'price' => $this->price
        ];
    }
}