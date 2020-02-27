<?php


namespace App\Http\Controllers\Client\Payment;


use App\Exceptions\Exception;
use App\Handlers\Client\Payment\OrderHandler;
use App\Http\Controllers\Controller;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function load()
    {
        return new JsonResponse([
            'methods' => config('site.payment.allowed_methods')
        ]);
    }

    public function order(Request $request, OrderHandler $handler)
    {
        try {
            $this->validate($request, [
                'method' => 'required|string',
                'sum' => 'required|integer|min:1'
            ]);

            $url = $handler->handle(
                Auth::getUser(),
                $request->post('method'),
                (int) $request->post('sum')
            );

            return new JsonResponse([
                'url' => $url
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}