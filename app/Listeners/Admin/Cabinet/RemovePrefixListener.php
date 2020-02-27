<?php


namespace App\Listeners\Admin\Cabinet;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\RemovePrefixEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class RemovePrefixListener implements Listener
{
    use SiteLogListener;

    /**
     * @param RemovePrefixEvent $event
     */
    public function writeToLogs(RemovePrefixEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            60,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param RemovePrefixEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}