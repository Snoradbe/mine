<?php


namespace App\Listeners\Admin\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\Log;
use App\Entity\Site\UserNotification;
use App\Events\Admin\BugReport\ChangeStatusEvent;
use App\Listeners\Listener;
use App\Listeners\NotificationListener;
use App\Listeners\SiteLogListener;

class ChangeStatusListener implements Listener
{
    use SiteLogListener, NotificationListener;

    /**
     * @param ChangeStatusEvent $event
     */
    public function writeToLogs(ChangeStatusEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getReport()->getServer(),
            54,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param ChangeStatusEvent $event
     */
    public function sendNotificationToUser(ChangeStatusEvent $event): void
    {
        $statusName = BugReport::getStatusName($event->getStatus());

        $this->sendNotification(new UserNotification(
            $event->getReport()->getUser(),
            sprintf('Статус вашего репорта #%d был изменен на %s', $event->getReport()->getId(), $statusName)
        ));
    }

    /**
     * @param ChangeStatusEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
        $this->sendNotificationToUser($event);
    }
}