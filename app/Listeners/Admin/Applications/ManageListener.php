<?php


namespace App\Listeners\Admin\Applications;


use App\Entity\Site\Application;
use App\Entity\Site\Log;
use App\Entity\Site\UserNotification;
use App\Events\Admin\Applications\ManageEvent;
use App\Listeners\Listener;
use App\Listeners\NotificationListener;
use App\Listeners\SiteLogListener;

class ManageListener implements Listener
{
    use SiteLogListener, NotificationListener;

    /**
     * @param ManageEvent $event
     */
    public function writeToLogs(ManageEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            0,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param ManageEvent $event
     */
    public function sendNotificationToUser(ManageEvent $event): void
    {
        $statusName = '?';
        switch ($event->getStatus())
        {
            case Application::ACCEPT: $statusName = 'принята.'; break;
            case Application::CANCEL: $statusName = 'отклонена.'; break;
            case Application::AGAIN: $statusName = 'отправлена на повторное заполнение.'; break;
        }

        $this->sendNotification(new UserNotification(
            $event->getApplication()->getUser(),
            sprintf(
                'Ваша заявка #%d на должность %s сервера %s была %s',
                $event->getApplication()->getId(),
                ucfirst($event->getApplication()->getPosition()),
                $event->getApplication()->getServer()->getName(),
                $statusName
            )
        ));
    }

    /**
     * @param ManageEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
        $this->sendNotificationToUser($event);
    }
}