<?php


namespace App\Http\Controllers\Admin\Settings;


use App\Handlers\Admin\Cabinet\Settings\SkinCloakSettingsHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Services\Auth\Auth;
use App\Services\Cabinet\CabinetEnum;
use App\Services\Cabinet\CabinetSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SkinCloakController extends Controller
{
    public function render()
    {
        NavMenu::$active = 'settings.skin-cloak';

        return view('admin.settings.skin-cloak', [
            'skin' => CabinetSettings::getSkinCloakSettings(CabinetEnum::SKIN_TYPE),
            'cloak' => CabinetSettings::getSkinCloakSettings(CabinetEnum::CLOAK_TYPE),
        ]);
    }

    public function save(Request $request, SkinCloakSettingsHandler $handler)
    {
        try {
            $this->validate($request, [
                'skin_w' => 'required|integer|min:0|max:9999',
                'skin_h' => 'required|integer|min:0|max:9999',
                'skin_hd_w' => 'required|integer|min:0|max:9999',
                'skin_hd_h' => 'required|integer|min:0|max:9999',
                'skin_size' => 'required|integer|min:0|max:9999',

                'cloak_w' => 'required|integer|min:0|max:9999',
                'cloak_h' => 'required|integer|min:0|max:9999',
                'cloak_hd_w' => 'required|integer|min:0|max:9999',
                'cloak_hd_h' => 'required|integer|min:0|max:9999',
                'cloak_size' => 'required|integer|min:0|max:9999',
            ]);

            $handler->handle(
                Auth::getUser(),
                [
                    'w' => (int) $request->post('skin_w'),
                    'h' => (int) $request->post('skin_h'),
                    'hd_w' => (int) $request->post('skin_hd_w'),
                    'hd_h' => (int) $request->post('skin_hd_h'),
                    'size' => (int) $request->post('skin_size'),
                ],
                [
                    'w' => (int) $request->post('cloak_w'),
                    'h' => (int) $request->post('cloak_h'),
                    'hd_w' => (int) $request->post('cloak_hd_w'),
                    'hd_h' => (int) $request->post('cloak_hd_h'),
                    'size' => (int) $request->post('cloak_size'),
                ]
            );

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors()->first());
        }
    }
}