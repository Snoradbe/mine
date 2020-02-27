<?php


namespace App\Listeners\Admin\Applications\Settings;


use App\Entity\Site\Log;
use App\Events\Admin\Applications\Settings\EditRulesEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditRulesListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditRulesEvent $event
     */
    public function writeToLogs(EditRulesEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            6,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditRulesEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}