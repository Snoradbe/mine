<?php


namespace App\Events\Client\Payment;


use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Event;

class OrderEvent extends ClientEvent implements Event
{
    /**
     * Метод олаты (unitpay)
     * @var string
     */
    private $method;

    /**
     * @var int
     */
    private $sum;

    /**
     * OrderEvent constructor.
     * @param User $user
     * @param string $method
     * @param int $sum
     */
    public function __construct(User $user, string $method, int $sum)
    {
        parent::__construct($user);

        $this->method = $method;
        $this->sum = $sum;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return int
     */
    public function getSum(): int
    {
        return $this->sum;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'sum' => $this->sum
        ];
    }
}