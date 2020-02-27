<?php


namespace App\Listeners\Admin\Cabinet;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\DeleteSkinCloakEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class DeleteSkinCloakListener implements Listener
{
    use SiteLogListener;

    /**
     * @param DeleteSkinCloakEvent $event
     */
    public function writeToLogs(DeleteSkinCloakEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            57,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param DeleteSkinCloakEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}