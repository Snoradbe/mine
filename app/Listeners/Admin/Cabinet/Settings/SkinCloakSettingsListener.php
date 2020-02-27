<?php


namespace App\Listeners\Admin\Cabinet\Settings;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\Settings\SkinCloakSettingsEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class SkinCloakSettingsListener implements Listener
{
    use SiteLogListener;

    /**
     * @param SkinCloakSettingsEvent $event
     */
    public function writeToLogs(SkinCloakSettingsEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            16,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param SkinCloakSettingsEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}