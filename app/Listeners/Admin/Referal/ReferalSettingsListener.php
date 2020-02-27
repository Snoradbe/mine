<?php


namespace App\Listeners\Admin\Referal;


use App\Entity\Site\Log;
use App\Events\Admin\Referal\ReferalSettingsEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class ReferalSettingsListener implements Listener
{
    use SiteLogListener;

    /**
     * @param ReferalSettingsEvent $event
     */
    public function writeToLogs(ReferalSettingsEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            51,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param ReferalSettingsEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}