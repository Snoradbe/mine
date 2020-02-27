<?php


namespace App\Listeners\Client\Applications;


use App\Entity\Site\Log;
use App\Events\Client\Applications\SendApplicationEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class SendApplicationListener implements Listener
{
    use SiteLogListener;

    /**
     * @param SendApplicationEvent $event
     */
    public function writeToLogs(SendApplicationEvent $event): void
    {
        $this->create(new Log(
            $event->getUser(),
            $event->getServer(),
            1005,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param SendApplicationEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}