<?php


namespace App\Http\Controllers\Client;


use App\Entity\Site\Log;
use App\Helpers\LogsHelper;
use App\Http\Controllers\Controller;
use App\Repository\Site\Log\LogRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Utils;

class LogsController extends Controller
{
    public function getLogs(LogRepository $logRepository, int $type)
    {
        $page = abs((int) request('page', 1));
        $logTypes = LogsHelper::getAllClientLogs();

        switch ($type)
        {
            case 0: $type = LogRepository::CLIENT_ALL_TYPE; break;
            case 1: $type = LogRepository::CLIENT_SPENT_TYPE; break;
            case 2: $type = LogRepository::CLIENT_RECEIVED_TYPE; break;

            default: $type = LogRepository::CLIENT_ALL_TYPE;
        }

        $logs = $logRepository->getAllClient(null, Auth::getUser(), array_keys($logTypes), $type, $page);

        return new JsonResponse([
            'logs' => array_map(function (Log $log) {
                return $log->toArray();
            }, $logs->all()),
            'types' => $logTypes,
            'pagination' => Utils::paginationData($logs)
        ]);
    }
}