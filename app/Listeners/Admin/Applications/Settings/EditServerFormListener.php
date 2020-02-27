<?php


namespace App\Listeners\Admin\Applications\Settings;


use App\Entity\Site\Log;
use App\Events\Admin\Applications\Settings\EditServerFormEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditServerFormListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditServerFormEvent $event
     */
    public function writeToLogs(EditServerFormEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            7,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditServerFormEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}