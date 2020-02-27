<?php


namespace App\Http\Controllers\Client;


use App\Exceptions\Exception;
use App\Handlers\Client\UnbanHandler;
use App\Http\Controllers\Controller;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;

class UnbanController extends Controller
{
    public function unban(UnbanHandler $handler)
    {
        try {
            $handler->handle(Auth::getUser());

            return new JsonResponse([
                'msg' => 'Вы успешно разбанились.<br>Возможно придется подождать несколько минут для входа на сервер'
            ]);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}