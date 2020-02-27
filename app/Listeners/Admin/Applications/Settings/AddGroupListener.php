<?php


namespace App\Listeners\Admin\Applications\Settings;


use App\Entity\Site\Log;
use App\Events\Admin\Applications\Settings\AddGroupEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class AddGroupListener implements Listener
{
    use SiteLogListener;

    /**
     * @param AddGroupEvent $event
     */
    public function writeToLogs(AddGroupEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            1,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddGroupEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}