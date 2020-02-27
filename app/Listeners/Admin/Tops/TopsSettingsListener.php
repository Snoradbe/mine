<?php


namespace App\Listeners\Admin\Tops;


use App\Entity\Site\Log;
use App\Events\Admin\Tops\TopsSettingsEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class TopsSettingsListener implements Listener
{
    use SiteLogListener;

    /**
     * @param TopsSettingsEvent $event
     */
    public function writeToLogs(TopsSettingsEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            29,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param TopsSettingsEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}