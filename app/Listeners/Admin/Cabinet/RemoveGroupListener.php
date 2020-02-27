<?php


namespace App\Listeners\Admin\Cabinet;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\RemoveGroupEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class RemoveGroupListener implements Listener
{
    use SiteLogListener;

    /**
     * @param RemoveGroupEvent $event
     */
    public function writeToLogs(RemoveGroupEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            59,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param RemoveGroupEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}