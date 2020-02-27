<?php


namespace App\Listeners\Admin\Cabinet\Settings\Groups;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\Settings\Groups\RemovePeriodEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class RemovePeriodListener implements Listener
{
    use SiteLogListener;

    /**
     * @param RemovePeriodEvent $event
     */
    public function writeToLogs(RemovePeriodEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            14,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param RemovePeriodEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}