<?php


namespace App\Listeners\Admin\ServerPermissions;


use App\Entity\Site\Log;
use App\Events\Admin\ServerPermissions\DeletePermissionEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class DeletePermissionListener implements Listener
{
    use SiteLogListener;

    /**
     * @param DeletePermissionEvent $event
     */
    public function writeToLogs(DeletePermissionEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            21,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param DeletePermissionEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}