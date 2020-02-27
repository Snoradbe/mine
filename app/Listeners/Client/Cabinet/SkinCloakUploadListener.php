<?php


namespace App\Listeners\Client\Cabinet;


use App\Entity\Site\Log;
use App\Events\Client\Cabinet\SkinCloakUploadEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class SkinCloakUploadListener implements Listener
{
    use SiteLogListener;

    /**
     * @param SkinCloakUploadEvent $event
     */
    public function writeToLogs(SkinCloakUploadEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            null,
            1004,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param SkinCloakUploadEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}