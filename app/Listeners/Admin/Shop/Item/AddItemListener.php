<?php


namespace App\Listeners\Admin\Shop\Item;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Item\AddItemEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class AddItemListener implements Listener
{
    use SiteLogListener;

    /**
     * @param AddItemEvent $event
     */
    public function writeToLogs(AddItemEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            39,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddItemEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}