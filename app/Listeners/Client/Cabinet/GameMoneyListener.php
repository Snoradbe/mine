<?php


namespace App\Listeners\Client\Cabinet;


use App\Entity\Site\Log;
use App\Events\Client\Cabinet\GameMoneyEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class GameMoneyListener implements Listener
{
    use SiteLogListener;

    /**
     * @param GameMoneyEvent $event
     */
    public function writeToLogs(GameMoneyEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            $event->getServer(),
            1011,
            $event->getIp(),
            $event->toArray(),
            $event->getPrice(),
            null,
            $event->getValute()
        ));
    }

    /**
     * @param GameMoneyEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}