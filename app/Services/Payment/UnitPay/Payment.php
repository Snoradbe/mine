<?php


namespace App\Services\Payment\UnitPay;


use App\Services\Payment\PaymentData;

class Payment
{
    private $paymentData;

    private $description;

    public function __construct(PaymentData $paymentData, string $description)
    {
        $this->paymentData = $paymentData;
        $this->description = $description;
    }

    public function getPaymentData(): PaymentData
    {
        return $this->paymentData;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}