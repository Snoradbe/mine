<?php


namespace App\Events\Client\Unban;


use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Event;

class UnbanEvent extends ClientEvent implements Event
{
    /**
     * @var int
     */
    private $price;

    /**
     * @var array
     */
    private $banData;

    /**
     * UnbanEvent constructor.
     * @param User $user
     * @param int $price
     * @param array $banData
     */
    public function __construct(User $user, int $price, array $banData)
    {
        parent::__construct($user);

        $this->price = $price;
        $this->banData = $banData;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return array
     */
    public function getBanData(): array
    {
        return $this->banData;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'price' => $this->price,
            'ban' => $this->banData
        ];
    }
}