<?php


namespace App\Listeners\Client\Cabinet;


use App\Entity\Site\Log;
use App\Events\Client\Cabinet\BuyGroupEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class BuyGroupListener implements Listener
{
    use SiteLogListener;

    /**
     * @param BuyGroupEvent $event
     */
    public function writeToLogs(BuyGroupEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            $event->getServer(),
            1001,
            $event->getIp(),
            $event->toArray(),
            $event->getSum(),
            null,
            'rub'
        ));
    }

    /**
     * @param BuyGroupEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}