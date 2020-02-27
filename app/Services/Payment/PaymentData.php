<?php


namespace App\Services\Payment;


use App\Entity\Site\User;

class PaymentData
{
    private $user;

    private $sum;

    private $method; //quiwi, yandex ...

    public function __construct(User $user, int $sum, ?string $method = null)
    {
        $this->user = $user;
        $this->sum = $sum;
        $this->method = $method;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getSum(): int
    {
        return $this->sum;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }
}