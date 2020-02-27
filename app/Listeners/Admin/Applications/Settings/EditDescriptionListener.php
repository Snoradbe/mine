<?php


namespace App\Listeners\Admin\Applications\Settings;


use App\Entity\Site\Log;
use App\Events\Admin\Applications\Settings\EditDescriptionEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditDescriptionListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditDescriptionEvent $event
     */
    public function writeToLogs(EditDescriptionEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            3,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditDescriptionEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}