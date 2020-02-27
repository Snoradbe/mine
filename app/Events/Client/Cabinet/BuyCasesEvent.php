<?php


namespace App\Events\Client\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Client\EventWithServer;
use App\Events\Event;

class BuyCasesEvent extends ClientEvent implements Event
{
    use EventWithServer;

    /**
     * @var array
     */
    private $cases;

    /**
     * BuyCasesEvent constructor.
     * @param User $user
     * @param Server $server
     * @param array $cases
     */
    public function __construct(User $user, Server $server, array $cases)
    {
        parent::__construct($user);

        $this->server = $server;
        $this->cases = $cases;
    }

    /**
     * @return int
     */
    public function getSum(): int
    {
        $sum = 0;
        foreach ($this->cases as $case => $amount)
        {
            $sum += $amount;
        }
        
        return $sum;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'cases' => $this->cases,
            'withdraw' => [
                'old' => $this->user->getOldCoins(),
                'sum' => $this->getSum()
            ]
        ];
    }
}