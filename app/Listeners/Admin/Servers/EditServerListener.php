<?php


namespace App\Listeners\Admin\Servers;


use App\Entity\Site\Log;
use App\Events\Admin\Servers\EditServerEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditServerListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditServerEvent $event
     */
    public function writeToLogs(EditServerEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getNew(),
            23,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditServerEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}