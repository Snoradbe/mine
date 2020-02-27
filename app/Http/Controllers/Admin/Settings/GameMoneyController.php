<?php


namespace App\Http\Controllers\Admin\Settings;


use App\Handlers\Admin\Cabinet\Settings\GameMoney\GameMoneySettingsHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use App\Services\Game\GameMoney\GameMoneySettings;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GameMoneyController extends Controller
{
    public function render(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'settings.game-money';

        //dd(GameMoneySettings::getManagers());

        return view('admin.settings.game-money', [
            'rates' => GameMoneySettings::getRates(),
            'servers' => $serverRepository->getAll(false),
            'managers' => GameMoneySettings::getManagers(),
            'managersList' => config('site.game_money.managers', [])
        ]);
    }

    public function save(Request $request, GameMoneySettingsHandler $handler)
    {
        //dd($request->post());
        try {
            $managersList = config('site.game_money.managers', []);
            $in = array_keys($managersList);

            $this->validate($request, [
                'default-rate' => 'required|integer|min:0|max:999999',
                'default-manager' => 'required|string|in:' . implode(',', $in),
                'rates' => 'required|array',
                'managers' => 'required|array'
            ]);

            $rates = $handler->filterRates($request->post('rates'));
            $rates['default'] = (int) $request->post('default-rate', 0);

            $managers = $handler->filterManagers($request->post('managers'));
            $managers['default'] = $managersList[$request->post('default-manager')];

            $handler->handle(
                Auth::getUser(),
                $rates,
                $managers
            );

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors()->first());
        }
    }
}