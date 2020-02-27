<?php


namespace App\Handlers\Admin\Logs;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Game\HawkLogs\HawkManager;
use App\Services\Game\HawkLogs\HawkSearch;
use App\Services\Permissions\Permissions;
use Illuminate\Pagination\LengthAwarePaginator;

class ServerLogsHandler
{
    private $hawkManager;

    private $serverRepository;

    public function __construct(HawkManager $manager, ServerRepository $serverRepository)
    {
        $this->hawkManager = $manager;
        $this->serverRepository = $serverRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(
        User $admin,
        int $serverId,
        array $actions,
        array $users,
        ?string $data,
        array $excludedData,
        array $xyz,
        ?int $range,
        array $dates,
        array $worlds,
        int $page): LengthAwarePaginator
    {
        $server = $this->getServer($serverId);
        if (
            !$admin->permissions()->hasMPPermission(Permissions::MP_LOGS_SERVER_ALL)
            &&
            !is_null($admin->permissions()->getServersWithPermission(Permissions::MP_LOGS_SERVER))
            &&
            !in_array($server, $admin->permissions()->getServersWithPermission(Permissions::MP_LOGS_SERVER))
        ) {
            throw new PermissionDeniedException();
        }

        return $this->hawkManager->getRepository($server)->getAll(new HawkSearch(
            $actions, $users, $data, $excludedData, $dates[0], $dates[1], $xyz[0], $xyz[1], $xyz[2], $range, $worlds
        ), $page);
    }
}