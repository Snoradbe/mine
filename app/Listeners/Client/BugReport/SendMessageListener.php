<?php


namespace App\Listeners\Client\BugReport;


use App\Entity\Site\Log;
use App\Events\Client\BugReport\SendMessageEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class SendMessageListener implements Listener
{
    use SiteLogListener;

    /**
     * @param SendMessageEvent $event
     */
    public function writeToLogs(SendMessageEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            $event->getReport()->getServer(),
            1013,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param SendMessageEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}