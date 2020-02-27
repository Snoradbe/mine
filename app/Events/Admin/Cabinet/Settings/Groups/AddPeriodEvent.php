<?php


namespace App\Events\Admin\Cabinet\Settings\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class AddPeriodEvent implements Event
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
    private $period;

    /**
     * @var int
     */
    private $oldPrice;

    /**
     * @var int
     */
    private $newPrice;

    /**
     * AddPeriodEvent constructor.
     * @param User $admin
     * @param Server $server
     * @param Group $group
     * @param int $period
     * @param int $oldPrice
     * @param int $newPrice
     */
    public function __construct(User $admin, Server $server, Group $group, int $period, int $oldPrice, int $newPrice)
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->group = $group;
        $this->period = $period;
        $this->oldPrice = $oldPrice;
        $this->newPrice = $newPrice;
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
    public function getPeriod(): int
    {
        return $this->period;
    }

    /**
     * @return int
     */
    public function getOldPrice(): int
    {
        return $this->oldPrice;
    }

    /**
     * @return int
     */
    public function getNewPrice(): int
    {
        return $this->newPrice;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'group' => $this->group->toArray(),
            'period' => $this->period,
            'old_price' => $this->oldPrice,
            'new_price' => $this->newPrice
        ];
    }
}