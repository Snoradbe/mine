<?php


namespace App\Listeners\Admin\Team;


use App\Entity\Site\Log;
use App\Events\Admin\Team\UpdateTeamEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class UpdateTeamListener implements Listener
{
    use SiteLogListener;

    /**
     * @param UpdateTeamEvent $event
     */
    public function writeToLogs(UpdateTeamEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            27,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param UpdateTeamEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}