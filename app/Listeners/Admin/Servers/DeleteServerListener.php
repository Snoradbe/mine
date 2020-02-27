<?php


namespace App\Listeners\Admin\Servers;


use App\Entity\Site\Log;
use App\Events\Admin\Servers\DeleteServerEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class DeleteServerListener implements Listener
{
    use SiteLogListener;

    /**
     * @param DeleteServerEvent $event
     */
    public function writeToLogs(DeleteServerEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            24,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param DeleteServerEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}