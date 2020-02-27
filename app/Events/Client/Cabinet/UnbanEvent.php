<?php


namespace App\Events\Client\Cabinet;


use App\Entity\Site\User;
use App\Events\Client\ClientEvent;

class UnbanEvent extends ClientEvent
{
    /**
     * @var int
     */
    private $sum;

    /**
     * UnbanEvent constructor.
     * @param User $user
     * @param int $sum
     */
    public function __construct(User $user, int $sum)
    {
        parent::__construct($user);

        $this->sum = $sum;
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
            'sum' => $this->sum,
            'old' => $this->user->getOldMoney()
        ];
    }
}