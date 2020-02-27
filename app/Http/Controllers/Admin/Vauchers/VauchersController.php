<?php


namespace App\Http\Controllers\Admin\Vauchers;


use App\Exceptions\Exception;
use App\Handlers\Admin\Vauchers\AddHandler;
use App\Handlers\Admin\Vauchers\DeleteHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Vaucher\VaucherRepository;
use App\Services\Auth\Auth;
use App\Services\Permissions\Permissions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VauchersController extends Controller
{
    public function render(VaucherRepository $repository)
    {
        NavMenu::$active = 'vauchers';

        $page = abs((int) request('page', 1));

        $types = [];
        foreach (config('site.vauchers.types', []) as $type => $data)
        {
            $types[$type] = $data['name'];
        }

        $user = Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_VAUCHERS_VIEW_ALL)
            ? null
            : Auth::getUser();

        return view('admin.vauchers.list', [
            'vauchers' => $repository->getAll($user, false, $page),
            'types' => $types,
            'canManage' => Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_ALL)
        ]);
    }

    public function add(Request $request, AddHandler $handler)
    {
        try {
            $this->validate($request, [
                'code' => 'nullable|string',
                'type' => 'required|in:' . implode(',',  array_keys(config('site.vauchers.types', []))),
                'message' => 'nullable|string',
                'reward' => 'required',
                'amount' => 'required|integer|min:-1|max:999',
                'count' => 'required|integer|min:1|max:50',
                'start' => 'nullable|date',
                'end' => 'nullable|date',
                'for' => 'nullable|string'
            ]);

            $handler->handle(
                Auth::getUser(),
                $request->post('code'),
                $request->post('type'),
                $request->post('message'),
                $request->post('reward'),
                (int) $request->post('amount'),
                (int) $request->post('count'),
                $request->post('start'),
                $request->post('end'),
                $request->post('for')
            );

            return redirect()->back()->with('success_message', 'Ваучер создан');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function delete(DeleteHandler $handler, int $id)
    {
        try {
            $handler->handle(Auth::getUser(), $id);

            return redirect()->back()->with('success_message', 'Ваучер удален');
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}