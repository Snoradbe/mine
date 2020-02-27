<?php


namespace App\Http\Controllers\Admin\Vauchers;


use App\Exceptions\Exception;
use App\Handlers\Admin\Vauchers\EditHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Vaucher\VaucherRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EditController extends Controller
{
    public function render(VaucherRepository $repository, int $id)
    {
        NavMenu::$active = 'vauchers';

        $vaucher = $repository->find($id);
        if (is_null($vaucher)) {
            return redirect()->route('admin.vauchers')->withErrors('Ваучер не найден!');
        }

        $types = [];
        foreach (config('site.vauchers.types', []) as $type => $data)
        {
            $types[$type] = $data['name'];
        }

        return view('admin.vauchers.edit', [
            'vaucher' => $vaucher,
            'types' => $types
        ]);
    }

    public function edit(Request $request, EditHandler $handler, int $id)
    {
        try {
            $this->validate($request, [
                'type' => 'required|in:' . implode(',',  array_keys(config('site.vauchers.types', []))),
                'message' => 'nullable|string',
                'reward' => 'required',
                'amount' => 'required|integer|min:-1|max:999',
                'start' => 'nullable|date',
                'end' => 'nullable|date',
                'for' => 'nullable|string'
            ]);

            $handler->handle(
                Auth::getUser(),
                $id,
                $request->post('type'),
                $request->post('message'),
                $request->post('reward'),
                (int) $request->post('amount'),
                $request->post('start'),
                $request->post('end'),
                $request->post('for')
            );

            return redirect()->back()->with('success_message', 'Ваучер изменен');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}