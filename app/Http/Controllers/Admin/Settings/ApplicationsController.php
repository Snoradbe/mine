<?php


namespace App\Http\Controllers\Admin\Settings;


use App\Exceptions\Exception;
use App\Handlers\Admin\Applications\Settings\AddGroupHandler;
use App\Handlers\Admin\Applications\Settings\DeleteGroupHandler;
use App\Handlers\Admin\Applications\Settings\EditCooldownHandler;
use App\Handlers\Admin\Applications\Settings\EditDescriptionHandler;
use App\Handlers\Admin\Applications\Settings\EditFormHandler;
use App\Handlers\Admin\Applications\Settings\EditGroupHandler;
use App\Handlers\Admin\Applications\Settings\EditGroupSelfHandler;
use App\Handlers\Admin\Applications\Settings\EditMinLevelHandler;
use App\Handlers\Admin\Applications\Settings\EditRulesHandler;
use App\Handlers\Admin\Applications\Settings\EditServerForm;
use App\Handlers\Admin\Applications\Settings\EditServerSelfForm;
use App\Helpers\GroupsHelper;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use App\Services\Permissions\Permissions;
use App\Services\Settings\DataType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplicationsController extends Controller
{
    public function render(ServerRepository $serverRepository, GroupRepository $groupRepository)
    {
        NavMenu::$active = 'settings.applications';

        $settings = settings('applications', DataType::JSON, []);
        $servers = [];

        $serverList = Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_APPLICATIONS_FORMS_ALL)
            ? $serverRepository->getAll()
            : Auth::getUser()->permissions()->getServersWithPermission(Permissions::MP_APPLICATIONS_FORMS_SERVER);

        if (is_null($serverList)) {
            $serverList = $serverRepository->getAll();
        }

        foreach ($serverList as $server)
        {
            $servers[$server->getId()] = $server;
        }

        return view('admin.settings.applications', [
            'apps' => $settings['statuses'],
            'servers' => $servers,
            'groups' => Auth::getUser()->permissions()->hasMPPermission(Permissions::ALL)
				? $groupRepository->getAllAdmin(true)
				: GroupsHelper::getAllowedManageGroups(Auth::getUser(), null, $groupRepository->getAllAdmin(true)),
            'cooldown' => $settings['cooldown'],
            'minLevel' => $settings['min_level'] ?? 1,
            'canAll' => Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_APPLICATIONS_FORMS_ALL)
        ]);
    }

    public function addGroup(Request $request, AddGroupHandler $handler)
    {
        try {
            $this->validate($request, [
                'group' => 'string|required',
                'name' => 'string|required'
            ]);

            $handler->handle(Auth::getUser(), $request->post('group'), $request->post('name'));

            return redirect()->back()->with('success_message', 'Группа добавена');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function editGroup(Request $request, EditGroupHandler $handler)
    {
        try {
            $this->validate($request, [
                'group' => 'string|required',
                'name' => 'string|required',
                'enabled' => 'array',
            ]);

            $handler->handle(Auth::getUser(), $request->post('group'), $request->post('name'), $request->post('enabled', []));

            return redirect()->back()->with('success_message', 'Настройки группы сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function editGroupSelf(Request $request, EditGroupSelfHandler $handler)
    {
        try {
            $this->validate($request, [
                'group' => 'string|required',
                'enabled' => 'array',
            ]);

            $handler->handle(Auth::getUser(), $request->post('group'), $request->post('enabled', []));

            return redirect()->back()->with('success_message', 'Настройки группы сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function deleteGroup(Request $request, DeleteGroupHandler $handler)
    {
        try {
            $this->validate($request, [
                'group' => 'string|required'
            ]);

            $handler->handle(Auth::getUser(), $request->post('group'));

            return redirect()->back()->with('success_message', 'Группа удалена');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function editForm(Request $request, EditFormHandler $handler)
    {
        try {
            $this->validate($request, [
                'group' => 'string|required',
                'form' => 'string|required'
            ]);

            $handler->handle(Auth::getUser(), $request->post('group'), $request->post('form'));

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function editDescription(Request $request, EditDescriptionHandler $handler)
    {
        try {
            $this->validate($request, [
                'group' => 'string|required',
                'descr' => 'string|required'
            ]);

            $handler->handle(Auth::getUser(), $request->post('group'), $request->post('descr'));

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function editRules(Request $request, EditRulesHandler $handler)
    {
        try {
            $this->validate($request, [
                'group' => 'string|required',
                'rules' => 'string|required'
            ]);

            $handler->handle(Auth::getUser(), $request->post('group'), $request->post('rules'));

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function editServerForm(Request $request, EditServerForm $handler)
    {
        try {
            $this->validate($request, [
                'group' => 'string|required',
                'server_form' => 'array|required'
            ]);

            $handler->handle(Auth::getUser(), $request->post('group'), $request->post('server_form'));

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function editServerSelfForm(Request $request, EditServerSelfForm $handler)
    {
        try {
            $this->validate($request, [
                'group' => 'string|required',
                'server_form' => 'array|required'
            ]);

            $handler->handle(Auth::getUser(), $request->post('group'), $request->post('server_form'));

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function editCooldown(Request $request, EditCooldownHandler $handler)
    {
        try {
            $this->validate($request, [
                'cooldown' => 'required|integer|min:0'
            ]);

            $handler->handle(Auth::getUser(), (int) $request->post('cooldown'));

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function editMinLevel(Request $request, EditMinLevelHandler $handler)
    {
        try {
            $this->validate($request, [
                'min_level' => 'required|integer|min:1'
            ]);

            $handler->handle(Auth::getUser(), (int) $request->post('min_level'));

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}