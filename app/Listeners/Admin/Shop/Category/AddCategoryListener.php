<?php


namespace App\Listeners\Admin\Shop\Category;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Category\AddCategoryEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class AddCategoryListener implements Listener
{
    use SiteLogListener;

    /**
     * @param AddCategoryEvent $event
     */
    public function writeToLogs(AddCategoryEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            37,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddCategoryEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}