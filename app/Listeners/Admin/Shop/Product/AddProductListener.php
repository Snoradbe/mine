<?php


namespace App\Listeners\Admin\Shop\Product;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Product\AddProductEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class AddProductListener implements Listener
{
    use SiteLogListener;

    /**
     * @param AddProductEvent $event
     */
    public function writeToLogs(AddProductEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            43,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddProductEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}