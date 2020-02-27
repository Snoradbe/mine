<?php


namespace App\Events\Api\Vote;


use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;
use App\Services\Voting\Tops\Top;

class VoteEvent implements Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Top
     */
    private $top;

    /**
     * VoteEvent constructor.
     * @param User $user
     * @param Top $top
     */
    public function __construct(User $user, Top $top)
    {
        $this->user = $user;
        $this->top = $top;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Top
     */
    public function getTop(): Top
    {
        return $this->top;
    }

    /**
     * @return int
     */
    public function getMoneyReward(): int
    {
        $sum = 0;
        $reward = $this->top->getRewards()['money'] ?? null;
        if (!is_null($reward)) {
            $sum = (int) $reward['amount'] ?? 0;
            if (($reward['7bonus'] ?? false) && (int) date('d') <= 7) {
                $sum *= 2;
            }
        }

        return $sum;
    }

    /**
     * @return int
     */
    public function getCoinsReward(): int
    {
        $sum = 0;
        $reward = $this->top->getRewards()['coins'] ?? null;
        if (!is_null($reward)) {
            $sum = (int) $reward['amount'] ?? 0;
            if (($reward['7bonus'] ?? false) && (int) date('d') <= 7) {
                $sum *= 2;
            }
        }

        return $sum;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'top' => $this->top->getName()
        ];
    }
}