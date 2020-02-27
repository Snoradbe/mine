<?php


namespace App\Listeners\Admin\SitePerms;


use App\Entity\Site\Log;
use App\Events\Admin\SitePerms\DeletePermissionsEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class DeletePermissionsListener implements Listener
{
    use SiteLogListener;

    /**
     * @param DeletePermissionsEvent $event
     */
    public function writeToLogs(DeletePermissionsEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            49,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param DeletePermissionsEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}