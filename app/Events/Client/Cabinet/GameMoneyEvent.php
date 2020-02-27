<?php


namespace App\Events\Client\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Client\EventWithServer;
use App\Events\Event;

class GameMoneyEvent extends ClientEvent implements Event
{
    use EventWithServer;

    /**
     * @var int
     */
    private $price;

    /**
     * @var string
     */
    private $valute;

    /**
     * @var int
     */
    private $amount;

    /**
     * GameMoneyEvent constructor.
     * @param User $user
     * @param Server $server
     * @param int $price
     * @param string $valute
     * @param int $amount
     */
    public function __construct(User $user, Server $server, int $price, string $valute, int $amount)
    {
        parent::__construct($user);

        $this->server = $server;
        $this->price = $price;
        $this->valute = $valute;
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getValute(): string
    {
        return $this->valute;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount
        ];
    }
}