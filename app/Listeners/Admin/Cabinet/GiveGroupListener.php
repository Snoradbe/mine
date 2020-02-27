<?php


namespace App\Listeners\Admin\Cabinet;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\GiveGroupEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class GiveGroupListener implements Listener
{
    use SiteLogListener;

    /**
     * @param GiveGroupEvent $event
     */
    public function writeToLogs(GiveGroupEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            58,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param GiveGroupEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}