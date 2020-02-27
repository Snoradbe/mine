<?php


namespace App\Http\Controllers\Admin\AdminPerms;


use App\Exceptions\Exception;
use App\Handlers\Admin\AdminPerms\AddPermissionsHandler;
use App\Handlers\Admin\AdminPerms\DeletePermissionsHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Group\GroupRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminPermsController extends Controller
{
    public function render(GroupRepository $groupRepository)
    {
        NavMenu::$active = 'admin.admin_perms';

        return view('admin.admin_perms.index', [
            'groups' => $groupRepository->getAllAdmin(),
            'permissionsList' => config('site.site_permissions'),
            'adminPermissions' => config('site.admin_permissions')
        ]);
    }

    public function add(Request $request, AddPermissionsHandler $handler)
    {
        try {
            $this->validate($request, [
                'groups' => 'required|array|min:1',
                'groups.*' => 'required|string',
                'permissions' => 'required|array|min:1',
                'permissions.*' => 'required|string|in:' . implode(',', array_keys(config('site.site_permissions', [])))
            ]);

            $handler->handle(
                Auth::getUser(),
                $request->post('groups'),
                $request->post('permissions')
            );

            return redirect()->back()->with('success_message', 'Права добавлены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function delete(Request $request, DeletePermissionsHandler $handler)
    {
        try {
            $this->validate($request, [
                'group' => 'required|string',
                'permissions' => 'required|array|min:1',
                'permissions.*' => 'required|string|in:' . implode(',', array_keys(config('site.site_permissions', [])))
            ]);

            $handler->handle(
                Auth::getUser(),
                $request->post('group'),
                $request->post('permissions')
            );

            return redirect()->back()->with('success_message', 'Права удалены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}