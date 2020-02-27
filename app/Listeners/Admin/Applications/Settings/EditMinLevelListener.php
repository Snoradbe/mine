<?php


namespace App\Listeners\Admin\Applications\Settings;


use App\Entity\Site\Log;
use App\Events\Admin\Applications\Settings\EditMinLevelEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditMinLevelListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditMinLevelEvent $event
     */
    public function writeToLogs(EditMinLevelEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            63,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditMinLevelEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}