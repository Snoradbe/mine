<?php


namespace App\Listeners;


use App\Entity\Site\UserNotification;
use App\Events\Event;
use App\Repository\Site\UserNotification\UserNotificationRepository;

trait NotificationListener
{
    protected function sendNotification(UserNotification $notification): void
    {
        app()->make(UserNotificationRepository::class)->create($notification);
    }

    public abstract function sendNotificationToUser(Event $event): void;
}