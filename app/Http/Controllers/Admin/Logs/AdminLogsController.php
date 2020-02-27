<?php


namespace App\Http\Controllers\Admin\Logs;


use App\Entity\Site\Log;
use App\Entity\Site\Server;
use App\Exceptions\Exception;
use App\Handlers\Admin\Logs\AdminLogsHandler;
use App\Helpers\LogsHelper;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use App\Services\Permissions\Permissions;
use App\Services\Response\JsonResponse;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminLogsController extends Controller
{
    public function render(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'logs.admin';

        /*$servers = Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_LOGS_SHOP_ALL)
            ? $serverRepository->getAll(false)
            : Auth::getUser()->permissions()->getServersWithPermission(Permissions::MP_LOGS_SHOP);*/

        return view('admin.logs.admin', [
            'servers' => array_map(function (Server $server) {
                return $server->toArray();
            }, $serverRepository->getAll(false)),
            'types' => array_merge(LogsHelper::getAdminLogs(), LogsHelper::getModerLogs())
        ]);
    }

    public function getLogs(Request $request, AdminLogsHandler $handler, ?int $server = null)
    {
        try {
            $this->validate($request, [
                'name' => 'nullable|string'
            ]);

            $page = abs((int) $request->get('page', 1));

            $logs = $handler->handle(Auth::getUser(), $server, $request->post('name'), $page);

            return new JsonResponse([
                'logs' => array_map(function (Log $log) {
                    return $log->toArray();
                }, $logs->all()),
                'pagination' => Utils::paginationData($logs)
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}