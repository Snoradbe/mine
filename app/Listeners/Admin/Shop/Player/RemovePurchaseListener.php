<?php


namespace App\Listeners\Admin\Shop\Player;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Player\RemovePurchaseEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class RemovePurchaseListener implements Listener
{
    use SiteLogListener;

    /**
     * @param RemovePurchaseEvent $event
     */
    public function writeToLogs(RemovePurchaseEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            42,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param RemovePurchaseEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}