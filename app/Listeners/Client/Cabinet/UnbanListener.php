<?php


namespace App\Listeners\Client\Cabinet;


use App\Entity\Site\Log;
use App\Events\Client\Cabinet\UnbanEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class UnbanListener implements Listener
{
    use SiteLogListener;

    /**
     * @param UnbanEvent $event
     */
    public function writeToLogs(UnbanEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            null,
            1004,
            $event->getIp(),
            $event->toArray(),
            $event->getSum(),
            null,
            'rub'
        ));
    }

    /**
     * @param UnbanEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}