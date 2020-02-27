<?php


namespace App\Http\Controllers\Admin\ServerPermissions;


use App\Entity\Game\PermissionsEx\Permission;
use App\Exceptions\Exception;
use App\Handlers\Admin\ServerPermissions\AddHandler;
use App\Handlers\Admin\ServerPermissions\DeleteHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Game\PermissionsEx\DoctrinePermissionsRepository;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ServerPermissionsController extends Controller
{
    public function render(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'admin.server_perms';

        return view('components.select_server_admin', [
            'servers' => $serverRepository->getAll(false),
            'route' => 'admin.server_perms.list'
        ]);
    }

    public function renderServer(GroupRepository $groupRepository, ServerRepository $serverRepository, int $server)
    {
        NavMenu::$active = 'admin.server_perms';

        $server = $serverRepository->find($server);
        if (is_null($server)) {
            return redirect()->route('admin.server_perms')->withErrors('Сервер не найден!');
        }

        /* @var DoctrinePermissionsRepository $repository */
        $repository = doctrine_connection(DoctrinePermissionsRepository::class, Permission::class, 'server_' . $server->getId());

        $permissions = [];

        foreach ($groupRepository->getAll('name', 'asc') as $group)
        {
            $permissions[$group->getName()] = [];
            foreach ($repository->getPermissionsByGroup($group->getName()) as $permission)
            {
                $permissions[$group->getName()][] = $permission;
            }
        }

        return view('admin.server_perms.list', [
            'permissions' => $permissions,
            'server' => $server
        ]);
    }

    public function add(Request $request, AddHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'groups' => 'required|string',
                'permissions' => 'required|string',
                'to_all' => 'nullable'
            ]);

            $handler->handle(
                Auth::getUser(),
                $server,
                $request->post('groups'),
                $request->post('permissions'),
                (bool) $request->post('to_all', false)
            );

            return redirect()->back()->with('success_message', 'Права добавлены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack()->withInput();
        }
    }

    public function delete(Request $request, DeleteHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'group' => 'required|string',
                'permissions' => 'required|array',
                'permissions.*' => 'required|string',
                'from_all' => 'nullable'
            ]);

            $handler->handle(
                Auth::getUser(),
                $server,
                $request->post('group'),
                $request->post('permissions'),
                (bool) $request->post('from_all', false)
            );

            return redirect()->back()->with('success_message', 'Права удалены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}