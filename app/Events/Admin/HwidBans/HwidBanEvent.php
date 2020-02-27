<?php


namespace App\Events\Admin\HwidBans;


use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class HwidBanEvent implements Event
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
     * HwidBanEvent constructor.
     * @param User $admin
     * @param User $target
     */
    public function __construct(User $admin, User $target)
    {
        $this->admin = $admin;
        $this->target = $target;
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
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}