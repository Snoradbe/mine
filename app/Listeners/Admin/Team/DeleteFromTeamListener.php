<?php


namespace App\Listeners\Admin\Team;


use App\Entity\Site\Log;
use App\Events\Admin\Team\DeleteFromTeamEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class DeleteFromTeamListener implements Listener
{
    use SiteLogListener;

    /**
     * @param DeleteFromTeamEvent $event
     */
    public function writeToLogs(DeleteFromTeamEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            28,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param DeleteFromTeamEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}