<?php


namespace App\Listeners\Admin\HwidBans;


use App\Entity\Site\Log;
use App\Events\Admin\HwidBans\HwidBanEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class HwidBanListener implements Listener
{
    use SiteLogListener;

    /**
     * @param HwidBanEvent $event
     */
    public function writeToLogs(HwidBanEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            65,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param HwidBanEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}