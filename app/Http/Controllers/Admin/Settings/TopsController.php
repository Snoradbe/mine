<?php


namespace App\Http\Controllers\Admin\Settings;


use App\Exceptions\Exception;
use App\Handlers\Admin\Tops\TopsSettingsHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Services\Auth\Auth;
use App\Services\Settings\DataType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TopsController extends Controller
{
    public function render()
    {
        NavMenu::$active = 'settings.tops';

        return view('admin.settings.tops.index', [
            'base' => settings('tops.base', DataType::JSON, []),
            'tops' => settings('tops.tops', DataType::JSON, [])
        ]);
    }

    public function save(Request $request, TopsSettingsHandler $handler)
    {
        //dd($request->all());

        $tops = settings('tops.tops', DataType::JSON, []);
        $rules = [
            'month_rewards' => 'required|array',
            'month_rewards.*' => 'required|integer|min:0|max:9999',
            'month_give_max' => 'nullable',
            'enabled' => 'nullable|array'
        ];

        foreach ($tops as $top => $data)
        {
            $rules['enabled.' . $top] = 'nullable';
            $rules['rewards.' . $top] = 'required|array|min:' . count($tops);
            $rules['rewards.' . $top . '.img'] = 'required|url';
            $rules['rewards.' . $top . '.url'] = 'required|url';
            $rules['rewards.' . $top . '.secret'] = 'required|string';
            $rules['rewards.' . $top . '.money'] = 'required|integer|min:0|max:9999';
            $rules['rewards.' . $top . '.money_7bonus'] = 'nullable';
            $rules['rewards.' . $top . '.coins'] = 'required|integer|min:0|max:9999';
            $rules['rewards.' . $top . '.coins_7bonus'] = 'nullable';
        }

        try {
            $this->validate($request, $rules);

            $handler->handle(
                Auth::getUser(),
                $request->post('month_rewards'),
                (bool) $request->post('month_give_max', false),
                $request->post('enabled'),
                $request->post('rewards')
            );

            return redirect()->back()->with('success_message', 'Настройки сохранены');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}