<?php


namespace App\Listeners\Admin\Team;


use App\Entity\Site\Log;
use App\Events\Admin\Team\AddToTeamEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class AddToTeamListener implements Listener
{
    use SiteLogListener;

    /**
     * @param AddToTeamEvent $event
     */
    public function writeToLogs(AddToTeamEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            25,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddToTeamEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}