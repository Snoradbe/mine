<?php


namespace App\Services\Payment\Payers;


use App\Exceptions\Exception;
use App\Services\Payment\PaymentData;
use App\Services\Payment\UnitPay\Checkout;
use App\Services\Payment\UnitPay\Payment;

class UnitPayPayer implements Payer
{
    private const NAME = 'unitpay';

    /**
     * IP адреса UNITPAY
     */
    private const IPs = [
        '31.186.100.49',
        '178.132.203.105',
        '52.29.152.23',
        '52.19.56.234'
    ];

    private $checkout;

    public function __construct(Checkout $checkout)
    {
        $this->checkout = $checkout;
    }

    public function paymentUrl(PaymentData $data): string
    {
        $desc = "Пополнение баланса игрока {$data->getUser()->getName()} на сумму {$data->getSum()} руб.";

        $payment = new Payment($data, $desc);

        return $this->checkout->paymentUrl($payment);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function validate(array $data, string $ip): bool
    {
        if(!in_array($ip, self::IPs)) {
            throw new Exception("IP $ip is not allowed!");
        }

        if($this->checkout->validate($data)) {
            if($data['method'] !== 'pay') {
                print $this->successAnswer();
                die;
            }
            return true;
        }

        return false;
    }

    public function nickname(array $data): string
    {
        return $data['params']['account'];
    }

    public function sum(array $data): int
    {
        return (int) $data['params']['sum']; //TODO: sum?
    }

    public function successAnswer(): string
    {
        return json_encode([
            "result" => [
                "message" => 'ОК'
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    public function errorAnswer(string $message): string
    {
        return json_encode([
            "error" => [
                //"code" => -32000,
                "message" => $message
            ]
        ], JSON_UNESCAPED_UNICODE);
    }
}