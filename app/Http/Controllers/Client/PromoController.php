<?php


namespace App\Http\Controllers\Client;


use App\Exceptions\Exception;
use App\Handlers\Client\PromoHandler;
use App\Http\Controllers\Controller;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PromoController extends Controller
{
    public function check(Request $request, PromoHandler $handler)
    {
        try {
            $this->validate($request, [
                'code' => 'required|string',
            ]);

            $data = $handler->handle(Auth::getUser(), $request->post('code'));

            return new JsonResponse($data);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}