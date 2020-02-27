<?php


namespace App\Handlers\Admin\Logs;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Helpers\LogsHelper;
use App\Repository\Site\Log\LogRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Permissions\Permissions;
use Illuminate\Pagination\LengthAwarePaginator;

class CabinetLogsHandler
{
    private $logRepository;

    private $serverRepository;

    public function __construct(LogRepository $logRepository, ServerRepository $serverRepository)
    {
        $this->logRepository = $logRepository;
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

    public function handle(User $admin, int $serverId, ?string $name, int $page): LengthAwarePaginator
    {
        $server = $this->getServer($serverId);
        if (
            !$admin->permissions()->hasMPPermission(Permissions::MP_LOGS_CABINET_ALL)
            &&
			!is_null($admin->permissions()->getServersWithPermission(Permissions::MP_LOGS_CABINET))
			&&
            !in_array($server, $admin->permissions()->getServersWithPermission(Permissions::MP_LOGS_CABINET))
        ) {
            throw new PermissionDeniedException();
        }

        return $this->logRepository->getAll($server, $name, array_keys(LogsHelper::getCabinetLogs()), $page);
    }
}