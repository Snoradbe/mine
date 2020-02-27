<?php


namespace App\Listeners\Admin\Vauchers;


use App\Entity\Site\Log;
use App\Events\Admin\Vauchers\AddVaucherEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class AddVaucherListener implements Listener
{
    use SiteLogListener;

    /**
     * @param AddVaucherEvent $event
     */
    public function writeToLogs(AddVaucherEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            30,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddVaucherEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}