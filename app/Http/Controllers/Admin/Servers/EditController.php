<?php


namespace App\Http\Controllers\Admin\Servers;


use App\Exceptions\Exception;
use App\Handlers\Admin\Servers\EditHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EditController extends Controller
{
    public function render(ServerRepository $serverRepository, int $id)
    {
        NavMenu::$active = 'admin.servers';

        $server = $serverRepository->find($id, false);
        if (is_null($server)) {
            return redirect()->route('admin.servers')->withErrors('Сервер не найден!');
        }

        return view('admin.servers.edit', [
            'server' => $server
        ]);
    }

    public function edit(Request $request, EditHandler $handler, int $id)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|min:2|max:36',
                'ip' => 'required|string|min:7|max:15',
                'port' => 'required|integer|min:1',
                'rcon_port' => 'required|integer|min:1',
                'enabled' => 'required|boolean'
            ]);

            $handler->handle(
                Auth::getUser(),
                $id,
                $request->post('name'),
                $request->post('ip'),
                (int) $request->post('port'),
                (int) $request->post('rcon_port'),
                (bool) $request->post('enabled')
            );

            return redirect()->back()->with('success_message', 'Сервер изменен');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}