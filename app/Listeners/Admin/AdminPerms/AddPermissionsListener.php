<?php


namespace App\Listeners\Admin\AdminPerms;


use App\Entity\Site\Log;
use App\Events\Admin\AdminPerms\AddPermissionsEvent;
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
            33,
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