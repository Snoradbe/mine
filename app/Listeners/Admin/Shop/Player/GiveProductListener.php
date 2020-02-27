<?php


namespace App\Listeners\Admin\Shop\Player;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Player\GiveProductEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class GiveProductListener implements Listener
{
    use SiteLogListener;

    /**
     * @param GiveProductEvent $event
     */
    public function writeToLogs(GiveProductEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            41,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param GiveProductEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}