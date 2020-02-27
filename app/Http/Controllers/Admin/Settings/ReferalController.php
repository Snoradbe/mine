<?php


namespace App\Http\Controllers\Admin\Settings;


use App\Exceptions\Exception;
use App\Handlers\Admin\Referal\ReferalSettingsHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Services\Auth\Auth;
use App\Services\Settings\DataType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReferalController extends Controller
{
    public function render()
    {
        NavMenu::$active = 'settings.referal';

        return view('admin.settings.referal', [
            'settings' => settings('referal.handlers', DataType::JSON, [])
        ]);
    }

    public function save(Request $request, ReferalSettingsHandler $handler)
    {
        try {
            $this->validate($request, [
                'percent' => 'required|integer|min:0|max:100',
                'levels' => 'required|array',
                'levels.*' => 'required|integer' //levels[2] = 10
            ]);

            $levels = [];
            foreach ($request->post('levels', []) as $level => $amount)
            {
                $level = (int) $level;
                if ($level < 2 || $level > 127) {
                    continue;
                }
                $levels[(int) $level] = (int) $amount;
            }

            ksort($levels);

            $handler->handle(Auth::getUser(), (int) $request->post('percent'), $levels);

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}