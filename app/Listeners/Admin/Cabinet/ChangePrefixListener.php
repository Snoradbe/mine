<?php


namespace App\Listeners\Admin\Cabinet;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\ChangePrefixEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class ChangePrefixListener implements Listener
{
    use SiteLogListener;

    /**
     * @param ChangePrefixEvent $event
     */
    public function writeToLogs(ChangePrefixEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            56,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param ChangePrefixEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}