<?php


namespace App\Events\Admin\Servers;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class DeleteServerEvent implements Event
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
     * AddServerEvent constructor.
     * @param User $admin
     * @param Server $server
     */
    public function __construct(User $admin, Server $server)
    {
        $this->admin = $admin;
        $this->server = $server;
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
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}