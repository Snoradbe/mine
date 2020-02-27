<?php


namespace App\Services\Cabinet\Vaucher\Handlers;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;

class MoneyHandler implements Handler
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
            throw new Exception('Количество рублей должно быть больше 0!');
        }

        return ['amount' => $amount, 'valute' => 'rub'];
    }

    public function handle(User $user, Vaucher $vaucher, string $message): array
    {
        $data = $vaucher->getValueArray();

        $amount = (int) $data['amount'] ?? 0;

        if ($amount < 1) {
            throw new Exception('Vaucher value `amount` is empty');
        }

        $user->depositMoney($amount);
        $this->userRepository->update($user);

        return [
            'amount' => $amount,
            'valute' => 'rub',
            'msg' => sprintf($message, $amount)
        ];
    }
}