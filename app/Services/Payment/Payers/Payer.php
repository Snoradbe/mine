<?php


namespace App\Services\Payment\Payers;


use App\Services\Payment\PaymentData;

interface Payer
{
    public function paymentUrl(PaymentData $data): string;

    public function getName(): string;

    public function validate(array $data, string $ip): bool;

    public function nickname(array $data): string;

    public function sum(array $data): int;

    public function successAnswer(): string;

    public function errorAnswer(string $message): string;
}