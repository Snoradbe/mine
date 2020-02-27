<?php


namespace App\Events\Admin\Servers;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class EditServerEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Server
     */
    private $old;

    /**
     * @var Server
     */
    private $new;

    /**
     * EditServerEvent constructor.
     * @param User $admin
     * @param Server $old
     * @param Server $new
     */
    public function __construct(User $admin, Server $old, Server $new)
    {
        $this->admin = $admin;
        $this->old = $old;
        $this->new = $new;
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
    public function getOld(): Server
    {
        return $this->old;
    }

    /**
     * @return Server
     */
    public function getNew(): Server
    {
        return $this->new;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'old' => $this->old->toArray(),
            'new' => $this->old->toArray()
        ];
    }
}