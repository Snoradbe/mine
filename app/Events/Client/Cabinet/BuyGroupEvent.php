<?php


namespace App\Events\Client\Cabinet;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserGroup;
use App\Events\Client\ClientEvent;
use App\Events\Client\EventWithServer;
use App\Events\Event;

class BuyGroupEvent extends ClientEvent implements Event
{
    use EventWithServer;

    /**
     * @var UserGroup
     */
    private $group;

    /**
     * @var int
     */
    private $period;

    /**
     * @var int
     */
    private $sum;

    /**
     * BuyGroupEvent constructor.
     * @param User $user
     * @param Server $server
     * @param UserGroup $group
     * @param int $period
     * @param int $sum
     */
    public function __construct(User $user, Server $server, UserGroup $group, int $period, int $sum)
    {
        parent::__construct($user);

        $this->server = $server;
        $this->group = $group;
        $this->period = $period;
        $this->sum = $sum;
    }

    /**
     * @return UserGroup
     */
    public function getGroup(): UserGroup
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
    public function getSum(): int
    {
        return $this->sum;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_group' => $this->group->toArray(false),
            'period' => $this->period,
            'sum' => $this->sum,
            'old' => $this->user->getOldMoney()
        ];
    }
}