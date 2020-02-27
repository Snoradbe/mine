<?php


namespace App\Handlers\Admin\Cabinet;


use App\Entity\Site\User;
use App\Events\Admin\Cabinet\SetValuteEvent;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;

class SetValuteHandler
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    private function getUser(int $id): User
    {
        $user = $this->userRepository->find($id);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    public function handle(User $admin, int $userId, string $type, int $amount): void
    {
        $target = $this->getUser($userId);

        switch ($type)
        {
            case 'money': $target->setMoney($amount); break;
            case 'coins': $target->setCoins($amount); break;
            default: throw new Exception('Тип валюты не найден!');
        }

        $this->userRepository->update($target);

        event(new SetValuteEvent($admin, $target, $type, $amount));
    }
}