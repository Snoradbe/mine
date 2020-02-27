<?php


namespace App\Listeners\Admin\ServerPermissions;


use App\Entity\Site\Log;
use App\Events\Admin\ServerPermissions\AddPermissionEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class AddPermissionListener implements Listener
{
    use SiteLogListener;

    /**
     * @param AddPermissionEvent $event
     */
    public function writeToLogs(AddPermissionEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            20,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddPermissionEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}