<?php


namespace App\Http\Controllers\Admin\Banlist;


use App\Exceptions\Exception;
use App\Handlers\Admin\Banlist\BanHandler;
use App\Handlers\Admin\Banlist\UnbanHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Game\LiteBans\LiteBansRepository;
use App\Services\Auth\Auth;
use App\Services\Permissions\Permissions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BansController extends Controller
{
    public function list(Request $request, LiteBansRepository $repository)
    {
        NavMenu::$active = 'banlist';

        $page = abs((int) $request->get('page', 1));

        return view('admin.banlist.bans', [
            'bans' => $repository->getAll($page),
            'canUnban' => Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_BANLIST_UNBAN),
            'canBan' => Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_BANLIST_BAN),
        ]);
    }

    public function ban(Request $request, BanHandler $handler)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|min:3|max:32',
                'reason' => 'nullable|string|max:255',
                'date' => 'nullable|date'
            ]);

            $handler->handle(
                Auth::getUser(),
                $request->post('name'),
                $request->post('reason'),
                $request->post('date')
            );

            return redirect()->back()->with('success_message', 'Вы успешно забанили игрока');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack()->withInput();
        }
    }

    public function unban(Request $request, UnbanHandler $handler)
    {
        try {
            $this->validate($request, [
                'id' => 'required|integer'
            ]);

            $handler->handle(Auth::getUser(), (int) $request->post('id'));

            return redirect()->back()->with('success_message', 'Вы успешно разбанили игрока');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}