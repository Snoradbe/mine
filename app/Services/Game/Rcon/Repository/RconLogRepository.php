<?php


namespace App\Services\Game\Rcon\Repository;


use App\Entity\Site\Server;
use App\Services\Game\Rcon\Entity\RconLog;

interface RconLogRepository
{
    public function getLastLogs(Server $server): array;

    public function create(RconLog $log): void;
}