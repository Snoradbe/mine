<?php


namespace App\Listeners\Admin\Vauchers;


use App\Entity\Site\Log;
use App\Events\Admin\Vauchers\DeleteVaucherEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class DeleteVaucherListener implements Listener
{
    use SiteLogListener;

    /**
     * @param DeleteVaucherEvent $event
     */
    public function writeToLogs(DeleteVaucherEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            32,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param DeleteVaucherEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}