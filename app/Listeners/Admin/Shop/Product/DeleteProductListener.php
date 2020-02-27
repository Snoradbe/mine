<?php


namespace App\Listeners\Admin\Shop\Product;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Product\DeleteProductEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class DeleteProductListener implements Listener
{
    use SiteLogListener;

    /**
     * @param DeleteProductEvent $event
     */
    public function writeToLogs(DeleteProductEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            45,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param DeleteProductEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}