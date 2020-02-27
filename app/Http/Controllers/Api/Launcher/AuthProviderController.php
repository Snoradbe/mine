<?php


namespace App\Http\Controllers\Api\Launcher;


use App\Exceptions\Exception;
use App\Handlers\Api\Launcher\AuthProviderHandler;
use App\Http\Controllers\Controller;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthProviderController extends Controller
{
    public function auth(Request $request, AuthProviderHandler $handler)
    {
        try {
            $this->validate($request, [
                'login' => 'required|string',
                'password' => 'required|string',
                'ip' => 'required|string'
            ]);

            $user = $handler->getUser($request->get('login'));

            if (!$handler->checkPassword($user, $request->get('password'))) {
                throw new Exception('Неправильный пароль!');
            }

            $ip = $request->get('ip');
            if ($user->getLastLoginIP() != $ip) {
                if ($user->hasG2fa()) {
                    //Если прошлый IP отличается от текущего и включен гугл аутентификатор, то шлем ошибку
                    throw new Exception('2fa');
                }

                $user->setLastLoginIP($ip);
                $handler->updateUser($user);
            }

            return new JsonResponse([
                'status' => 1,
                'response' => 'OK:' . $user->getName()
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse([
                'status' => 2,
                'response' => $exception->validator->errors()->first()
            ]);
        } catch (Exception $exception) {
            return new JsonResponse([
                'status' => 2,
                'response' => $exception->getMessage()
            ]);
        }
    }

    public function a2fa(Request $request, AuthProviderHandler $handler)
    {
        try {
            $this->validate($request, [
                'login' => 'required|string',
                'code' => 'required|string',
                'ip' => 'required|string'
            ]);

            $user = $handler->getUser($request->get('login'));
            if (!$user->hasG2fa()) {
                throw new Exception('У вас не включена двухфакторная авторизация!');
            }

            if (!$handler->checkGoogle($user, $request->get('code'))) {
                throw new Exception('Неверный код!');
            }

            $user->setLastLoginIP($request->get('ip'));
            $handler->updateUser($user);

            return new JsonResponse([
                'status' => 1,
                'response' => 'ok'
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse([
                'status' => 2,
                'response' => $exception->validator->errors()->first()
            ]);
        } catch (Exception $exception) {
            return new JsonResponse([
                'status' => 2,
                'response' => $exception->getMessage()
            ]);
        }
    }
}