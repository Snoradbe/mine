<?php


namespace App\Listeners\Admin\Schematics;


use App\Entity\Site\Log;
use App\Events\Admin\Schematics\UploadSchematicEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class UploadSchematicListener implements Listener
{
    use SiteLogListener;

    /**
     * @param UploadSchematicEvent $event
     */
    public function writeToLogs(UploadSchematicEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            35,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param UploadSchematicEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}