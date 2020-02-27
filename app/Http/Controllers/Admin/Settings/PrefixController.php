<?php


namespace App\Http\Controllers\Admin\Settings;


use App\Exceptions\Exception;
use App\Handlers\Admin\Cabinet\Settings\Prefix\PrefixSettingsHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Services\Auth\Auth;
use App\Services\Cabinet\CabinetSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PrefixController extends Controller
{
    public function render()
    {
        NavMenu::$active = 'settings.prefix';

        return view('admin.settings.prefix', [
            'settings' => CabinetSettings::getPrefixSettings(),
            'colors' => config('site.colors', []),
            'allowedColors' => array_keys(CabinetSettings::getPrefixSettings()['colors'])
        ]);
    }

    public function save(Request $request, PrefixSettingsHandler $handler)
    {
        try {
            $this->validate($request, [
                'colors' => 'required|array',
                'min' => 'required|integer|min:0|max:99',
                'max' => 'required|integer|min:0|max:99',
                'regex' => 'required|string'
            ]);

            $colors = [];
            foreach (config('site.colors', []) as $color => $bg)
            {
                if (in_array($color, $request->post('colors'))) {
                    $colors[$color] = $bg;
                }
            }

            $handler->handle(
                Auth::getUser(),
                $colors,
                (int) $request->post('min'),
                (int) $request->post('max'),
                strip_tags($request->post('regex'))
            );

            return redirect()->back()->with('success_message', 'Настройки префиксов сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors()->first());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}