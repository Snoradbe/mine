<?php


namespace App\Listeners\Admin\Banlist;


use App\Entity\Site\Log;
use App\Events\Admin\Banlist\BanEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class BanListener implements Listener
{
    use SiteLogListener;

    /**
     * @param BanEvent $event
     */
    public function writeToLogs(BanEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            8,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param BanEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}