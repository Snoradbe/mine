<?php


namespace App\Listeners\Admin\Vauchers;


use App\Entity\Site\Log;
use App\Events\Admin\Vauchers\EditVaucherEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditVaucherListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditVaucherEvent $event
     */
    public function writeToLogs(EditVaucherEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            31,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditVaucherEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}