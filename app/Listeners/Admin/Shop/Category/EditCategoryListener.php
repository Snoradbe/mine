<?php


namespace App\Listeners\Admin\Shop\Category;


use App\Entity\Site\Log;
use App\Events\Admin\Shop\Category\EditCategoryEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class EditCategoryListener implements Listener
{
    use SiteLogListener;

    /**
     * @param EditCategoryEvent $event
     */
    public function writeToLogs(EditCategoryEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            38,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param EditCategoryEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}