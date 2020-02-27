<?php


namespace App\Listeners\Client\BugReport;


use App\Entity\Site\Log;
use App\Events\Client\BugReport\SendReportEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class SendReportListener implements Listener
{
    use SiteLogListener;

    /**
     * @param SendReportEvent $event
     */
    public function writeToLogs(SendReportEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            $event->getReport()->getServer(),
            1012,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param SendReportEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}