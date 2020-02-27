<?php


namespace App\Http\Controllers\Admin\Servers;


use App\Exceptions\Exception;
use App\Handlers\Admin\Servers\AddHandler;
use App\Handlers\Admin\Servers\DeleteHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ListController extends Controller
{
    public function render(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'admin.servers';

        return view('admin.servers.list', [
            'servers' => $serverRepository->getAll(false)
        ]);
    }

    public function add(Request $request, AddHandler $handler)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|min:2|max:36',
                'ip' => 'required|string|min:7|max:15',
                'port' => 'required|integer|min:1',
                'rcon_port' => 'required|integer|min:1'
            ]);

            $server = $handler->handle(
                Auth::getUser(),
                $request->post('name'),
                $request->post('ip'),
                (int) $request->post('port'),
                (int) $request->post('rcon_port')
            );

            return redirect()->route('admin.servers.edit', ['id' => $server->getId()])->with('success_message', 'Сервер добавлен');
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

            return redirect()->route('admin.servers')->with('success_message', 'Сервер был удален');
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}