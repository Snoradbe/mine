<?php


namespace App\Listeners\Admin\Shop\Product\Discount;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Product\Discount\SetDiscountEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class SetDiscountListener implements Listener
{
    use SiteLogListener;

    /**
     * @param SetDiscountEvent $event
     */
    public function writeToLogs(SetDiscountEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            46,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param SetDiscountEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}