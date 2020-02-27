<?php


namespace App\Services\Voting\RewardHandlers;


use App\Entity\Site\User;

class MoneyHandler implements RewardHandler
{
    public function handle(User $user, array $data): void
    {
        $bonus7day = $data['7bonus'] ?? false;
        $sum = (int) ($data['amount'] ?? 0);
        if ($bonus7day && (int) date('d') <= 7) {
            $sum *= 2;
        }

        $user->depositMoney($sum);
    }
}