<?php


namespace App\Listeners\Client\Unban;


use App\Entity\Site\Log;
use App\Events\Client\Unban\UnbanEvent;
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
            1010,
            $event->getIp(),
            $event->toArray(),
            $event->getPrice(),
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