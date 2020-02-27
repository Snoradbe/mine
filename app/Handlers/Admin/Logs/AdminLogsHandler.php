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

class AdminLogsHandler
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
        $server = $this->serverRepository->find($id, false);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $admin, ?int $server, ?string $name, int $page): LengthAwarePaginator
    {
        if (!is_null($server)) {
            $server = $this->getServer($server);
        }
        /*if (
            !$admin->permissions()->hasMPPermission(Permissions::MP_LOGS_CABINET_ALL)
            &&
            !in_array($server, $admin->permissions()->getServersWithPermission(Permissions::MP_LOGS_CABINET))
        ) {
            throw new PermissionDeniedException();
        }*/

        $actions = array_merge(array_keys(LogsHelper::getAdminLogs()), array_keys(LogsHelper::getModerLogs()));

        return $this->logRepository->getAll($server, $name, $actions, $page);
    }
}