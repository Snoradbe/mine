<?php


namespace App\Events\Api\User;


use App\Entity\Site\User;
use App\Events\Event;

class PaymentEvent implements Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $sum;

    /**
     * Метод оплаты (unitpay...)
     *
     * @var string
     */
    private $method;

    /**
     * PaymentEvent constructor.
     * @param User $user
     * @param int $sum
     * @param string $method
     */
    public function __construct(User $user, int $sum, string $method)
    {
        $this->user = $user;
        $this->sum = $sum;
        $this->method = $method;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getSum(): int
    {
        return $this->sum;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'sum' => $this->sum,
            'method' => $this->method
        ];
    }
}