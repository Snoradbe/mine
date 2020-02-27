<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\Exception;
use App\Handlers\Api\Payment\PayHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay(Request $request, PayHandler $handler, string $payer)
    {
        try {
            $msg = $handler->handle($payer, $request->all(), $request->ip());

            return $msg;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}