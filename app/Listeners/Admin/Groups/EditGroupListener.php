<?php


namespace App\Listeners\Admin\Groups;


use App\Entity\Site\Log;
use App\Events\Admin\Groups\EditGroupEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditGroupListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditGroupEvent $event
     */
    public function writeToLogs(EditGroupEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            18,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditGroupEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}