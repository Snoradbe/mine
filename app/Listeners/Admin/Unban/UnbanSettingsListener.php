<?php


namespace App\Listeners\Admin\Unban;


use App\Entity\Site\Log;
use App\Events\Admin\Unban\UnbanSettingsEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class UnbanSettingsListener implements Listener
{
    use SiteLogListener;

    /**
     * @param UnbanSettingsEvent $event
     */
    public function writeToLogs(UnbanSettingsEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            50,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param UnbanSettingsEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}