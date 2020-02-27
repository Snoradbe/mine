<?php


namespace App\Listeners\Api\User;


use App\Entity\Site\UserNotification;
use App\Events\Api\User\PaymentEvent;
use App\Listeners\Listener;
use App\Listeners\NotificationListener;
use App\Services\Referal\Handlers\PaymentHandler;
use App\Services\Settings\DataType;

class PaymentListener implements Listener
{
    use NotificationListener;

    /**
     * @param PaymentEvent $event
     */
    public function sendNotificationToUser(PaymentEvent $event): void
    {
        //
    }

    private function sendToUser(PaymentEvent $event): void
    {
        $this->sendNotification(new UserNotification(
            $event->getUser(),
            sprintf('На ваш счет было зачислено %d руб., спасибо что помогаете нам развиваться :)', $event->getSum())
        ));
    }

    private function sendToReferer(PaymentEvent $event, int $sum): void
    {
        $this->sendNotification(new UserNotification(
            $event->getUser()->getReferer(),
            sprintf(
                'Ваш реферал %s пополнил свой счет на сумму %d руб., ваш доход составил %d руб.',
                $event->getUser()->getName(),
                $event->getSum(),
                $sum
            )
        ));
    }

    /**
     * @param PaymentEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->sendToUser($event);

        //Выдаем процент рефереру, если есть
        if (!is_null($event->getUser()->getReferer())) {
            $handlers = settings('referal.handlers', DataType::JSON, []);
            $type = 'percent';

            if (isset($handlers[$type])) {
                $reward = $handlers[$type];

                $percentage = app()->make(PaymentHandler::class)
                    ->handle($event->getUser(), $type, ['reward' => $reward, 'base_sum' => $event->getSum()]);

                if ($percentage > 0) {
                    $this->sendToReferer($event, $percentage);
                }
            }
        }
    }
}