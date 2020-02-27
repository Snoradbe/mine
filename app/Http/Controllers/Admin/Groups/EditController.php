<?php


namespace App\Http\Controllers\Admin\Groups;


use App\Exceptions\Exception;
use App\Handlers\Admin\Groups\EditHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Group\GroupRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EditController extends Controller
{
    public function render(GroupRepository $groupRepository, int $id)
    {
        NavMenu::$active = 'admin.groups';

        $group = $groupRepository->find($id);
        if (is_null($group)) {
            return redirect()->route('admin.groups')->withErrors('Группа не найдена!');
        }

        return view('admin.groups.edit', [
            'group' => $group,
            'groups' => $groupRepository->getAll()
        ]);
    }

    public function edit(Request $request, EditHandler $handler, int $id)
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

            $handler->handle(
                Auth::getUser(),
                $id,
                $request->get('parent'),
                strtolower($request->post('name')),
                (int) $request->post('weight'),
                (bool) $request->post('is_primary'),
                (bool) $request->post('is_admin'),
                $request->post('permission_name'),
                $request->get('forum_id')
            );

            return redirect()->back()->with('success_message', 'Группа изменена');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}