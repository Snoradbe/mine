<?php


namespace App\Handlers\Client\Payment;


use App\Entity\Site\User;
use App\Events\Client\Payment\OrderEvent;
use App\Exceptions\Exception;
use App\Services\Payment\Payers\Pool;
use App\Services\Payment\PaymentData;

class OrderHandler
{
    private $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    public function handle(User $user, string $method, int $sum): string
    {
        $payer = $this->pool->find($method);
        if(is_null($payer)) {
            throw new Exception("Метод оплаты '{$method}' не найден!");
        }

        event(new OrderEvent($user, $method, $sum));

        return $payer->paymentUrl(new PaymentData($user, $sum, 'qiwi'));
    }
}