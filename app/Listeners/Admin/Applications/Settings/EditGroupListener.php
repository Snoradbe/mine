<?php


namespace App\Listeners\Admin\Applications\Settings;


use App\Entity\Site\Log;
use App\Events\Admin\Applications\Settings\EditGroupEvent;
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
            5,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditGroupEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}