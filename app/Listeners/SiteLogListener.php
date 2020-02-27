<?php


namespace App\Listeners;


use App\Entity\Site\Log;
use App\Events\Event;
use App\Repository\Site\Log\LogRepository;

trait SiteLogListener
{
    protected function create(Log $log): void
    {
        app()->make(LogRepository::class)->create($log);
    }

    public abstract function writeToLogs(Event $event): void;
}