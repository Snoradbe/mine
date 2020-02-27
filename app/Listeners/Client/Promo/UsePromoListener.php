<?php


namespace App\Listeners\Client\Promo;


use App\Entity\Site\Log;
use App\Events\Client\Promo\UsePromoEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class UsePromoListener implements Listener
{
    use SiteLogListener;

    /**
     * @param UsePromoEvent $event
     */
    public function writeToLogs(UsePromoEvent $event): void
    {
        $received = $event->getReceived();
        $receivedSum = is_null($received) ? null : $received[0];
        $receivedValute = is_null($received) ? null : $received[1];

        $this->create(new Log(
            $event->getUser(),
            null,
            1009,
            $event->getIp(),
            $event->toArray(),
            null,
            $receivedSum,
            $receivedValute
        ));
    }

    /**
     * @param UsePromoEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}