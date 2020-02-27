<?php


namespace App\Listeners\Api\Vote;


use App\Entity\Site\Log;
use App\Events\Api\Vote\VoteEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class VoteListener implements Listener
{
    use SiteLogListener;

    /**
     * @param VoteEvent $event
     */
    public function writeToLogs(VoteEvent $event): void
    {
        $money = $event->getMoneyReward();
        if ($money > 0) {
            $this->create(new Log(
                $event->getUser(),
                null,
                1016,
                'API',
                $event->toArray(),
                null,
                $money,
                'rub'
            ));
        }

        $coins = $event->getCoinsReward();
        if ($coins > 0) {
            $this->create(new Log(
                $event->getUser(),
                null,
                1016,
                'API',
                $event->toArray(),
                null,
                $coins,
                'coins'
            ));
        }
    }

    /**
     * @param VoteEvent $event
     */
    public function handle($event): void
    {
       $this->writeToLogs($event);
    }
}