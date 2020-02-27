<?php


namespace App\Http\Controllers\Client\Cabinet;


use App\Exceptions\Exception;
use App\Handlers\Client\Cabinet\BuyCasesHandler;
use App\Http\Controllers\Controller;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CasesController extends Controller
{
    public function buy(Request $request, BuyCasesHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'amount' => 'required|integer|min:1|max:32'
            ]);

            [$message, $amount] = $handler->handle(Auth::getUser(), $server, (int) $request->post('amount'));

            return new JsonResponse([
                'msg' => $message,
                'amount' => $amount
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}