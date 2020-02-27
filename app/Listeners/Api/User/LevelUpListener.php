<?php


namespace App\Listeners\Api\User;


use App\Entity\Site\UserNotification;
use App\Events\Api\User\LevelUpEvent;
use App\Listeners\Listener;
use App\Listeners\NotificationListener;
use App\Services\Referal\Handlers\LevelUpHandler;
use App\Services\Settings\DataType;

class LevelUpListener implements Listener
{
    use NotificationListener;

    /**
     * @param LevelUpEvent $event
     */
    public function sendNotificationToUser(LevelUpEvent $event): void
    {
        $this->sendNotification(new UserNotification(
            $event->getUser(),
            sprintf('Вы достигли %d уровня', $event->getNewLevel())
        ));
    }

    /**
     * @param LevelUpEvent $event
     */
    public function handle($event): void
    {
        $this->sendNotificationToUser($event);

        //Выдаем награду рефереру, если она есть
        if (!is_null($event->getUser()->getReferer())) {
            $handlers = settings('referal.handlers', DataType::JSON, []);
            $type = 'level_' . $event->getNewLevel();

            if (isset($handlers[$type])) {
                $rewards = $handlers[$type];

                app()->make(LevelUpHandler::class)
                    ->handle($event->getUser(), $type, ['reward' => $rewards]);
            }
        }
    }
}