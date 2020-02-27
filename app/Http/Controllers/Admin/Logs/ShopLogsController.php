<?php


namespace App\Http\Controllers\Admin\Logs;


use App\Entity\Site\Log;
use App\Entity\Site\Server;
use App\Exceptions\Exception;
use App\Handlers\Admin\Logs\ShopLogsHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use App\Services\Permissions\Permissions;
use App\Services\Response\JsonResponse;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ShopLogsController extends Controller
{
    public function render(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'logs.shop';

        $servers = Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_LOGS_SHOP_ALL)
            ? $serverRepository->getAll(false)
            : Auth::getUser()->permissions()->getServersWithPermission(Permissions::MP_LOGS_SHOP);

        if (is_null($servers)) {
            $servers = $serverRepository->getAll(false);
        }

        return view('admin.logs.shop', [
            'servers' => array_map(function (Server $server) {
                return $server->toArray();
            }, $servers)
        ]);
    }

    public function getLogs(Request $request, ShopLogsHandler $handler, int $server)
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