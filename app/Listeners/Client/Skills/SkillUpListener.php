<?php


namespace App\Listeners\Client\Skills;


use App\Entity\Site\Log;
use App\Events\Client\Skills\SkillUpEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class SkillUpListener implements Listener
{
    use SiteLogListener;

    /**
     * @param SkillUpEvent $event
     */
    public function writeToLogs(SkillUpEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            null,
            1014,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param SkillUpEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}