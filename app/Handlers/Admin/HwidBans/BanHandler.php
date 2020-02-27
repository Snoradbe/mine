<?php


namespace App\Handlers\Admin\HwidBans;


use App\Entity\Site\User;
use App\Events\Admin\HwidBans\HwidBanEvent;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;

class BanHandler
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    private function getUser(string $name): User
    {
        $user = $this->userRepository->findByName($name);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    public function handle(User $admin, string $name): void
    {
        $user = $this->getUser($name);
        $user->hwidBan(true);
        $this->userRepository->update($user);

        event(new HwidBanEvent($admin, $user));    }
}