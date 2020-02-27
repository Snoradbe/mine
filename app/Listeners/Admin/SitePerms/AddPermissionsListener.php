<?php


namespace App\Listeners\Admin\SitePerms;


use App\Entity\Site\Log;
use App\Events\Admin\SitePerms\AddPermissionsEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class AddPermissionsListener implements Listener
{
    use SiteLogListener;

    /**
     * @param AddPermissionsEvent $event
     */
    public function writeToLogs(AddPermissionsEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            48,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddPermissionsEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}