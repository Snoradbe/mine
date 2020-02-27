<?php


namespace App\Http\Controllers\Admin\HwidBans;


use App\Exceptions\Exception;
use App\Handlers\Admin\HwidBans\BanHandler;
use App\Handlers\Admin\HwidBans\UnbanHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\User\UserRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HwidBansController extends Controller
{
    public function render(UserRepository $userRepository)
    {
        NavMenu::$active = 'hwid_bans';

        $page = abs((int) request('page', 1));

        return view('admin.hwid_bans.index', [
            'users' => $userRepository->getHwidBannedUsers($page)
        ]);
    }

    public function ban(Request $request, BanHandler $handler)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|min:2'
            ]);

            $handler->handle(Auth::getUser(), $request->post('name'));

            return redirect()->back()->with('success_message', 'Игрок был забанен!');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }

    public function unban(Request $request, UnbanHandler $handler)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|min:2'
            ]);

            $handler->handle(Auth::getUser(), $request->post('name'));

            return redirect()->back()->with('success_message', 'Игрок был разбанен');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}