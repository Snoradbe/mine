<?php


namespace App\Services\Game\HawkLogs;


use App\Entity\Game\HawkEye\HawkLog;
use App\Entity\Site\Server;
use App\Repository\Game\HawkEye\DoctrineHawkLogRepository;
use App\Repository\Game\HawkEye\HawkLogRepository;

class HawkManager
{
    public function getRepository(Server $server): HawkLogRepository
    {
        return doctrine_connection(
            DoctrineHawkLogRepository::class,
            HawkLog::class,
            'server_' . $server->getId() . '_logs'
            );
    }
}