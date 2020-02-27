<?php


namespace App\Listeners\Admin\BugReport;


use App\Entity\Site\Log;
use App\Entity\Site\UserNotification;
use App\Events\Admin\BugReport\SendMessageEvent;
use App\Listeners\Listener;
use App\Listeners\NotificationListener;
use App\Listeners\SiteLogListener;

class SendMessageListener implements Listener
{
    use SiteLogListener, NotificationListener;

    /**
     * @param SendMessageEvent $event
     */
    public function writeToLogs(SendMessageEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getReport()->getServer(),
            55,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param SendMessageEvent $event
     */
    public function sendNotificationToUser(SendMessageEvent $event): void
    {
        $this->sendNotification(new UserNotification(
            $event->getReport()->getUser(),
            sprintf('Администрация ответила на ваш репорт #%d', $event->getReport()->getId())
        ));
    }

    /**
     * @param SendMessageEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
        $this->sendNotificationToUser($event);
    }
}