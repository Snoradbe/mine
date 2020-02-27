<?php


namespace App\Http\Controllers\Admin\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Exceptions\Exception;
use App\Handlers\Admin\Cabinet\Settings\Groups\AddGroupHandler;
use App\Handlers\Admin\Cabinet\Settings\Groups\AddPeriodHandler;
use App\Handlers\Admin\Cabinet\Settings\Groups\RemoveGroupHandler;
use App\Handlers\Admin\Cabinet\Settings\Groups\RemovePeriodHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use App\Services\Cabinet\CabinetSettings;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GroupsController extends Controller
{
    public function render2(AddGroupHandler $handler)
    {
        $handler->handle(1, 'premium', 300);
    }

    public function render(GroupRepository $groupRepository, ServerRepository $serverRepository)
    {
        NavMenu::$active = 'settings.groups';

        $servers = $serverRepository->getAll(false);
        $groups = $groupRepository->getAllDonate();

        $primary = CabinetSettings::getGroupsSettings(true);
        $other = CabinetSettings::getGroupsSettings(false);

        $addGroup = function (Server $server, Group $group, array $groups, array &$list)
        {
            if (isset($groups[$server->getId()]) && isset($groups[$server->getId()][$group->getName()])) {
                $list[] = [
                    'group' => $group->toArray(),
                    'periods' => $groups[$server->getId()][$group->getName()]
                ];
            }
        };

        $list = [];
        foreach ($servers as $server)
        {
            $data = [
                'server' => $server->toArray(),
                'groups' => []
            ];

            foreach ($groups as $group)
            {
                $addGroup($server, $group, $primary, $data['groups']);
                $addGroup($server, $group, $other, $data['groups']);
            }

            $list[] = $data;
        }

        return view('admin.settings.groups', [
            'groups' => $list,
            'allGroups' => array_map(function (Group $group) {
                return $group->toArray();
            }, $groups)
        ]);
    }

    public function addGroup(Request $request, AddGroupHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'group' => 'required|string',
                'price' => 'required|integer|min:1|max:99999',
            ]);

            $handler->handle(
                Auth::getUser(),
                $server,
                $request->post('group'),
                (int) $request->post('price')
            );

            return new JsonResponse(['msg' => 'Вы успешно добавили группу']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }

    public function removeGroup(Request $request, RemoveGroupHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'group' => 'required|string',
            ]);

            $handler->handle(
                Auth::getUser(),
                $server,
                $request->post('group')
            );

            return new JsonResponse(['msg' => 'Вы успешно удалили группу']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }

    public function addPeriod(Request $request, AddPeriodHandler $handler, int $server, string $group)
    {
        try {
            $this->validate($request, [
                'period' => 'required|integer|min:-1|max:999',
                'price' => 'required|integer|min:1|max:99999',
            ]);

            $handler->handle(
                Auth::getUser(),
                $server,
                $group,
                (int) $request->post('period'),
                (int) $request->post('price')
            );

            return new JsonResponse(['msg' => 'Вы успешно добавили период']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }

    public function removePeriod(Request $request, RemovePeriodHandler $handler, int $server, string $group)
    {
        try {
            $this->validate($request, [
                'period' => 'required|integer|min:-1|max:999',
            ]);

            $handler->handle(
                Auth::getUser(),
                $server,
                $group,
                (int) $request->post('period')
            );

            return new JsonResponse(['msg' => 'Вы успешно удалили период']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}