<?php


namespace App\Http\Controllers\Admin\Team;


use App\Exceptions\Exception;
use App\Handlers\Admin\Team\AddHandler;
use App\Handlers\Admin\Team\DeleteHandler;
use App\Handlers\Admin\Team\ListHandler;
use App\Handlers\Admin\Team\TransitHandler;
use App\Handlers\Admin\Team\UpdateHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Services\Auth\Auth;
use App\Services\Permissions\Permissions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ListController extends Controller
{
    public function render(ListHandler $handler)
    {
        NavMenu::$active = 'team';

        $user = Auth::getUser();

        [$list, $listMass, $servers, $allowedGroups] = $handler->handle($user);

        return view('admin.team.list', [
            'list' => $list,
            'mass' => $listMass,
            'servers' => $servers,
            'groups' => $allowedGroups,
            'canUpgrade' => $user->permissions()->hasMPPermission(Permissions::MP_TEAM_UPGRADE),
            'canTransit' => $user->permissions()->hasMPPermission(Permissions::MP_TEAM_TRANSIT),
            'canAdd' => $user->permissions()->hasMPPermission(Permissions::MP_TEAM_ADD),
            'canRemove' => $user->permissions()->hasMPPermission(Permissions::MP_TEAM_REMOVE),
            'canMass' => $user->permissions()->hasMPPermission(Permissions::MP_TEAM_VIEW_ALL),
            'user' => $user
        ]);
    }

    public function add(Request $request, AddHandler $handler)
    {
        try {
            $this->validate($request, [
                'name' => 'required|min:2|max:255',
                'server' => 'required|integer',
                'group' => 'required|integer'
            ]);

            $handler->handle(
                Auth::getUser(),
                $request->post('name'),
                (int) $request->post('server'),
                (int) $request->post('group')
            );

            return redirect()->back()->with('success_message', 'Игрок был добавлен в команду администрации');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack()->withInput();
        }
    }

    public function transit(Request $request, TransitHandler $handler)
    {
        try {
            $this->validate($request, [
                'server' => 'required|integer',
                'id' => 'required|integer'
            ]);

            $handler->handle(
                Auth::getUser(),
                (int) $request->post('id'),
                (int) $request->post('server')
            );

            return redirect()->back()->with('success_message', 'Игрок был переведен на новый сервер');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack()->withInput();
        }
    }

    public function update(Request $request, UpdateHandler $handler)
    {
        try {
            $this->validate($request, [
                'id' => 'required|integer',
                'group' => 'required|integer'
            ]);

            $handler->handle(
                Auth::getUser(),
                (int) $request->post('id'),
                (int) $request->post('group')
            );

            return redirect()->back()->with('success_message', 'Группа игрока была обновлена');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack()->withInput();
        }
    }

    public function delete(Request $request, DeleteHandler $handler)
    {
        try {
            $this->validate($request, [
                'id' => 'required|integer'
            ]);

            $handler->handle(
                Auth::getUser(),
                (int) $request->post('id')
            );

            return redirect()->back()->with('success_message', 'Игрок был разжалован');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack()->withInput();
        }
    }
}