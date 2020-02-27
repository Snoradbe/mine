<?php


namespace App\Http\Controllers\Admin\Schematics;


use App\Exceptions\Exception;
use App\Handlers\Admin\Schematics\UploadHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use App\Services\Permissions\Permissions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SchematicsController extends Controller
{
    public function render(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'schematics';

        $servers = Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_SCHEMATICS_UPLOAD_ALL)
            ? $serverRepository->getAll(false)
            : Auth::getUser()->permissions()->getServersWithPermission(Permissions::MP_SCHEMATICS_UPLOAD);

        if (is_null($servers)) {
            $servers = $serverRepository->getAll(false);
        }

        return view('admin.schematics.index', [
            'servers' => $servers
        ]);
    }

    public function upload(Request $request, UploadHandler $handler)
    {
        try {
            $this->validate($request, [
                'server' => 'required|integer',
                'file' => 'required|file|min:1|max:512'
            ]);

            $name = $handler->handle(Auth::getUser(), $request->file('file'), (int) $request->post('server'));

            return redirect()->back()
                ->with('success_message', 'Схематик загружен. Для вставки используйте: /schem load ' . $name);
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}