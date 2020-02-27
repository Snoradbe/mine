<?php


namespace App\Listeners\Admin\Cabinet;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\SetValuteEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class SetValuteListener implements Listener
{
    use SiteLogListener;

    /**
     * @param SetValuteEvent $event
     */
    public function writeToLogs(SetValuteEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            61,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param SetValuteEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}