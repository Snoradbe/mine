<?php


namespace App\Listeners\Client\Cabinet;


use App\Entity\Site\Log;
use App\Events\Client\Cabinet\SkinCloakDeleteEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class SkinCloakDeleteListener implements Listener
{
    use SiteLogListener;

    /**
     * @param SkinCloakDeleteEvent $event
     */
    public function writeToLogs(SkinCloakDeleteEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            null,
            1003,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param SkinCloakDeleteEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}