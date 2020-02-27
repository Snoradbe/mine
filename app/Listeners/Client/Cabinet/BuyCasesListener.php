<?php


namespace App\Listeners\Client\Cabinet;


use App\Entity\Site\Log;
use App\Events\Client\Cabinet\BuyCasesEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class BuyCasesListener implements Listener
{
    use SiteLogListener;

    /**
     * @param BuyCasesEvent $event
     */
    public function writeToLogs(BuyCasesEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            $event->getServer(),
            1000,
            $event->getIp(),
            $event->toArray(),
            $event->getSum()
        ));
    }

    /**
     * @param BuyCasesEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}