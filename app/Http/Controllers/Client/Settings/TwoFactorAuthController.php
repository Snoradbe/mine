<?php


namespace App\Http\Controllers\Client\Settings;


use App\Exceptions\Exception;
use App\Handlers\Client\Settings\Google2faHandler;
use App\Http\Controllers\Controller;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TwoFactorAuthController extends Controller
{
    public function googleCheck(Google2faHandler $handler)
    {
        return new JsonResponse([
            'secret' => $handler->generateSecret()
        ]);
    }

    public function googleEnable(Request $request, Google2faHandler $handler)
    {
        try {
            $this->validate($request, [
                'secret' => 'required|string|min:16|max:16',
                'code' => 'required|string'
            ]);

            $handler->enable(Auth::getUser(), $request->post('secret'), $request->post('code'));

            return new JsonResponse(['msg' => 'Вы успешно включили гугл-аутентификатор']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }

    public function googleDisable(Request $request, Google2faHandler $handler)
    {
        try {
            $this->validate($request, [
                'code' => 'required|string'
            ]);

            $handler->disable(Auth::getUser(), $request->post('code'));

            return new JsonResponse(['msg' => 'Вы успешно отключили гугл-аутентификатор']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => 'Введите код!'], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}