<?php


namespace App\Listeners\Admin\Applications\Settings;


use App\Entity\Site\Log;
use App\Events\Admin\Applications\Settings\EditServerSelfFormEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditServerSelfFormListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditServerSelfFormEvent $event
     */
    public function writeToLogs(EditServerSelfFormEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            64,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditServerSelfFormEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}