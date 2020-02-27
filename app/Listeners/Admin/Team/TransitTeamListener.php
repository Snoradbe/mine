<?php


namespace App\Listeners\Admin\Team;


use App\Entity\Site\Log;
use App\Events\Admin\Team\TransitTeamEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class TransitTeamListener implements Listener
{
    use SiteLogListener;

    /**
     * @param TransitTeamEvent $event
     */
    public function writeToLogs(TransitTeamEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            26,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param TransitTeamEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}