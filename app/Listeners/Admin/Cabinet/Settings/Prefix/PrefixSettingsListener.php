<?php


namespace App\Listeners\Admin\Cabinet\Settings\Prefix;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\Settings\Prefix\PrefixSettingsEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class PrefixSettingsListener implements Listener
{
    use SiteLogListener;

    /**
     * @param PrefixSettingsEvent $event
     */
    public function writeToLogs(PrefixSettingsEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            15,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param PrefixSettingsEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}