<?php


namespace App\Http\Controllers\Admin\Groups;


use App\Exceptions\Exception;
use App\Handlers\Admin\Groups\AddHandler;
use App\Handlers\Admin\Groups\DeleteHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Group\GroupRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ListController extends Controller
{
    public function render(GroupRepository $groupRepository)
    {
        NavMenu::$active = 'admin.groups';

        return view('admin.groups.list', [
            'groups' => $groupRepository->getAll()
        ]);
    }

    public function add(Request $request, AddHandler $handler)
    {
        try {
            $this->validate($request, [
                'parent' => 'nullable|integer',
                'name' => 'required|string|min:2|max:24|regex:/(^[a-z0-9_\-]+$)/',
                'weight' => 'required|integer',
                'is_primary' => 'required|boolean',
                'is_admin' => 'required|boolean',
                'permission_name' => 'nullable|string|regex:/(^[A-Za-z0-9\.\-\_]+$)/',
                'forum_id' => 'nullable|integer',
            ]);

            if (!$request->post('is_primary') && empty($request->post('permission_name'))) {
                throw new Exception('Введите название пермишена!');
            }

            $server = $handler->handle(
                Auth::getUser(),
                $request->get('parent'),
                strtolower($request->post('name')),
                (int) $request->post('weight'),
                (bool) $request->post('is_primary'),
                (bool) $request->post('is_admin'),
                $request->post('permission_name'),
                $request->get('forum_id')
            );

            return redirect()->route('admin.groups.edit', ['id' => $server->getId()])->with('success_message', 'Группа добавлена');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack()->withInput();
        }
    }

    public function delete(DeleteHandler $handler, int $id)
    {
        try {
            $handler->handle(Auth::getUser(), $id);

            return redirect()->route('admin.groups')->with('success_message', 'Группа была удалена');
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}