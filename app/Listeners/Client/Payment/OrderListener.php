<?php


namespace App\Listeners\Client\Payment;


use App\Entity\Site\Log;
use App\Events\Client\Payment\OrderEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class OrderListener implements Listener
{
    use SiteLogListener;

    /**
     * @param OrderEvent $event
     */
    public function writeToLogs(OrderEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            null,
            1006,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param OrderEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}