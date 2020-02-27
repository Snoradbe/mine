<?php


namespace App\Listeners\Admin\Applications\Settings;


use App\Entity\Site\Log;
use App\Events\Admin\Applications\Settings\EditCooldownEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditCooldownListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditCooldownEvent $event
     */
    public function writeToLogs(EditCooldownEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            62,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditCooldownEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}