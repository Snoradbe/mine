<?php


namespace App\Listeners\Admin\HwidBans;


use App\Entity\Site\Log;
use App\Events\Admin\HwidBans\HwidUnbanEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class HwidUnbanListener implements Listener
{
    use SiteLogListener;

    /**
     * @param HwidUnbanEvent $event
     */
    public function writeToLogs(HwidUnbanEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            66,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param HwidUnbanEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}