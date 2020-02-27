<?php


namespace App\Listeners\Admin\Shop\Item;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Item\EditItemEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditItemListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditItemEvent $event
     */
    public function writeToLogs(EditItemEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            40,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditItemEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}