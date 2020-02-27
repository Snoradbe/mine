<?php


namespace App\Http\Controllers\Admin\SitePerms;


use App\Exceptions\Exception;
use App\Handlers\Admin\SitePerms\AddPermissionsHandler;
use App\Handlers\Admin\SitePerms\DeletePermissionsHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Group\GroupRepository;
use App\Services\Auth\Auth;
use App\Services\Cabinet\CabinetUtils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SitePermsController extends Controller
{
    public function render(GroupRepository $groupRepository)
    {
        NavMenu::$active = 'admin.site_perms';

        return view('admin.site_perms.index', [
            'groups' => array_merge($groupRepository->getAll(), [CabinetUtils::getDefaultGroup()]),
            'permissionsList' => config('site.site_permissions'),
            'cabinetPermissions' => array_merge(config('site.cabinet_permissions'), config('site.shop_permissions'))
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