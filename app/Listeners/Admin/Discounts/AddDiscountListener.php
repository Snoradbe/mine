<?php


namespace App\Listeners\Admin\Discounts;


use App\Entity\Site\Log;
use App\Events\Admin\Discounts\AddDiscountEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class AddDiscountListener implements Listener
{
    use SiteLogListener;

    /**
     * @param AddDiscountEvent $event
     */
    public function writeToLogs(AddDiscountEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            52,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddDiscountEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}