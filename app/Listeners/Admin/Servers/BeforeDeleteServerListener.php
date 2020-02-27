<?php


namespace App\Listeners\Admin\Servers;


use App\Events\Admin\Servers\BeforeDeleteServerEvent;
use App\Listeners\Listener;

class BeforeDeleteServerListener implements Listener
{
    /**
     * @param BeforeDeleteServerEvent $event
     */
    public function handle(/*BeforeDeleteServerEvent */$event): void
    {
        //
    }
}