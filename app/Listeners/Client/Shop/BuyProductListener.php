<?php


namespace App\Listeners\Client\Shop;


use App\Entity\Site\Log;
use App\Events\Client\Shop\BuyProductEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class BuyProductListener implements Listener
{
    use SiteLogListener;

    /**
     * @param BuyProductEvent $event
     */
    public function writeToLogs(BuyProductEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            $event->getServer(),
            1007,
            $event->getIp(),
            $event->toArray(),
            $event->getPrice(),
            null,
            $event->getValute()
        ));
    }

    /**
     * @param BuyProductEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}