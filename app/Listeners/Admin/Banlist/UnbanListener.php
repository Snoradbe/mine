<?php


namespace App\Listeners\Admin\Banlist;


use App\Entity\Site\Log;
use App\Events\Admin\Banlist\UnbanEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class UnbanListener implements Listener
{
    use SiteLogListener;

    /**
     * @param UnbanEvent $event
     */
    public function writeToLogs(UnbanEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            9,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param UnbanEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}