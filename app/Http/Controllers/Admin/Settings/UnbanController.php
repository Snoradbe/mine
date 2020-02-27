<?php


namespace App\Http\Controllers\Admin\Settings;


use App\Exceptions\Exception;
use App\Handlers\Admin\Unban\UnbanSettingsHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Services\Auth\Auth;
use App\Services\Settings\DataType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UnbanController extends Controller
{
    public function render()
    {
        NavMenu::$active = 'settings.unban';

        return view('admin.settings.unban', [
            'price' => settings('unban', DataType::INT, 9999)
        ]);
    }

    public function save(Request $request, UnbanSettingsHandler $handler)
    {
        try {
            $this->validate($request, [
                'price' => 'required|integer|min:1|max:9999'
            ]);

            $handler->handle(Auth::getUser(), (int) $request->post('price'));

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}