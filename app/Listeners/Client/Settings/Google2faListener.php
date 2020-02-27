<?php


namespace App\Listeners\Client\Settings;


use App\Entity\Site\Log;
use App\Events\Client\Settings\Google2faEvent;
use App\Events\Event;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class Google2faListener implements Listener
{
    use SiteLogListener;

    /**
     * @param Google2faEvent $event
     */
    public function writeToLogs(Google2faEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            null,
            1017,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param Google2faEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}