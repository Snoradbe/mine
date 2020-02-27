<?php


namespace App\Listeners\Admin\Groups;


use App\Entity\Site\Log;
use App\Events\Admin\Groups\DeleteGroupEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class DeleteGroupListener implements Listener
{
    use SiteLogListener;

    /**
     * @param DeleteGroupEvent $event
     */
    public function writeToLogs(DeleteGroupEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            19,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param DeleteGroupEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}