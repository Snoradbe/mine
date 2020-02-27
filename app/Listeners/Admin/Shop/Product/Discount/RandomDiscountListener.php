<?php


namespace App\Listeners\Admin\Shop\Product\Discount;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Product\Discount\RandomDiscountEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class RandomDiscountListener implements Listener
{
    use SiteLogListener;

    /**
     * @param RandomDiscountEvent $event
     */
    public function writeToLogs(RandomDiscountEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            47,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param RandomDiscountEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}