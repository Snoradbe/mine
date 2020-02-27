<?php


namespace App\Listeners\Client\Shop;


use App\Entity\Site\Log;
use App\Events\Client\Shop\CancelPurchaseEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class CancelPurchaseListener implements Listener
{
    use SiteLogListener;

    /**
     * @param CancelPurchaseEvent $event
     */
    public function writeToLogs(CancelPurchaseEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            $event->getServer(),
            1008,
            $event->getIp(),
            $event->toArray(),
            null,
            $event->getResultSum() > 0 ? $event->getResultSum() : null,
            $event->getSum() > 0 ? $event->getValute() : null
        ));
    }

    /**
     * @param CancelPurchaseEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}