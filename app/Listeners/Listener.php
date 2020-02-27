<?php


namespace App\Listeners;


use App\Events\Event;

interface Listener
{
    /**
     * @param Event $event
     * @return void
     */
    public function handle($event): void;
}