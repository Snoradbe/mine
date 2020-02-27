<?php


namespace App\Http\Controllers\Admin\Logs;


use App\Entity\Site\Server;
use App\Exceptions\Exception;
use App\Handlers\Admin\Logs\ServerLogsHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use App\Services\Game\HawkLogs\HawkSearch;
use App\Services\Permissions\Permissions;
use App\Services\Response\JsonResponse;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ServerLogsController extends Controller
{
    public function render(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'logs.server';

        $servers = Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_LOGS_SERVER_ALL)
            ? $serverRepository->getAll(false)
            : Auth::getUser()->permissions()->getServersWithPermission(Permissions::MP_LOGS_SERVER);

        if (is_null($servers)) {
            $servers = $serverRepository->getAll(false);
        }

        return view('admin.logs.server', [
            'servers' => array_map(function (Server $server) {
                return $server->toArray();
            }, $servers),
            'actions' => config('site.hawkeye.actions', [])
        ]);
    }

    public function getLogs(Request $request, ServerLogsHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'user' => 'nullable|string',
                'actions' => 'array',
                'actions.*' => 'integer',
                'excluded_data' => 'nullable|string',
                'x' => 'nullable|integer',
                'y' => 'nullable|integer',
                'z' => 'nullable|integer',
                'radius' => 'nullable|integer|min:0|max:' . HawkSearch::MAX_RANGE,
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
                'worlds' => 'nullable|string',
            ]);

            $page = (int) $request->get('page', 1);
            $actions = [];
            foreach ($request->post('actions', []) as $action)
            {
                $i = (int) $action;
                if (!in_array($i, $actions)) {
                    $actions[] = $i;
                }
            }

            $user = $request->post('user');
            if (!empty($user)) {
                $users = explode(',', $user);
            } else {
                $users = [];
            }

            $excludedData = $request->post('excluded_data');
            if (!empty($excludedData)) {
                $excludedDatas = explode(',', $excludedData);
            } else {
                $excludedDatas = [];
            }

            $worlds = $request->post('worlds');
            if (!empty($worlds)) {
                $worlds = explode(',', $worlds);
            } else {
                $worlds = [];
            }

            $xyz = [null, null, null];
            $x = $request->post('x');
            if (!is_null($x)) {
                $xyz[0] = (int) $x;
            }
            $y = $request->post('y');
            if (!is_null($y)) {
                $xyz[1] = (int) $y;
            }
            $z = $request->post('z');
            if (!is_null($z)) {
                $xyz[2] = (int) $z;
            }

            $radius = $request->post('radius');
            if (!is_null($radius)) {
                $radius = (int) $radius;
            }

            $dates = [null, null];
            $dateFrom = $request->post('date_from');
            if (!is_null($dateFrom)) {
                try {
                    $dates[0] = (new \DateTime($dateFrom))->format('Y-m-d H:i:s');
                } catch (\Exception $exception) {
                    //
                }
            }
            $dateTo = $request->post('date_to');
            if (!is_null($dateTo)) {
                try {
                    $dates[1] = (new \DateTime($dateTo))->format('Y-m-d H:i:s');
                } catch (\Exception $exception) {
                    //
                }
            }

            $logs = $handler->handle(
                Auth::getUser(),
                $server,
                $actions,
                $users,
                $request->post('data'),
                $excludedDatas,
                $xyz,
                $radius,
                $dates,
                $worlds,
                $page
            );

            return new JsonResponse([
                'logs' => $logs->all(),
                'pagination' => Utils::paginationData($logs)
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }
}