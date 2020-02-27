<?php


namespace App\Listeners\Admin\Cabinet\Settings\Groups;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\Settings\Groups\AddPeriodEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class AddPeriodListener implements Listener
{
    use SiteLogListener;

    /**
     * @param AddPeriodEvent $event
     */
    public function writeToLogs(AddPeriodEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            13,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddPeriodEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}