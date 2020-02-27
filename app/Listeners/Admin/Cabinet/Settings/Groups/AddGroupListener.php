<?php


namespace App\Listeners\Admin\Cabinet\Settings\Groups;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\Settings\Groups\AddGroupEvent;
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
            $event->getServer(),
            11,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddGroupEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}