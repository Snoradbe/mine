<?php


namespace App\Listeners\Admin\Discounts;


use App\Entity\Site\Log;
use App\Events\Admin\Discounts\DeleteDiscountEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class DeleteDiscountListener implements Listener
{
    use SiteLogListener;

    /**
     * @param DeleteDiscountEvent $event
     */
    public function writeToLogs(DeleteDiscountEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            53,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param DeleteDiscountEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}