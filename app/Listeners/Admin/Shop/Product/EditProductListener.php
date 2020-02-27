<?php


namespace App\Listeners\Admin\Shop\Product;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Product\EditProductEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditProductListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditProductEvent $event
     */
    public function writeToLogs(EditProductEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            44,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditProductEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}