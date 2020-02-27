<?php


namespace App\Services\Cabinet\Vaucher\Handlers;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;

class CoinsHandler implements Handler
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getReward(string $post)
    {
        $amount = abs((int)$post);
        if ($amount == 0) {
            throw new Exception('Количество монет должно быть больше 0!');
        }

        return ['amount' => $amount, 'valute' => 'coins'];
    }

    public function handle(User $user, Vaucher $vaucher, string $message): array
    {
        $data = $vaucher->getValueArray();

        $amount = (int) $data['amount'] ?? 0;

        if ($amount < 1) {
            throw new Exception('Vaucher value `amount` is empty');
        }

        $user->depositCoins($amount);
        $this->userRepository->update($user);

        return [
            'amount' => $amount,
            'valute' => 'coins',
            'msg' => sprintf($message, $amount)
        ];
    }
}