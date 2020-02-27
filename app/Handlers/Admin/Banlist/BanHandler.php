<?php


namespace App\Handlers\Admin\Banlist;


use App\Entity\Game\LiteBans\LiteBansBan;
use App\Entity\Site\User;
use App\Events\Admin\Banlist\BanEvent;
use App\Exceptions\Exception;
use App\Repository\Game\LiteBans\LiteBansRepository;
use App\Repository\Site\User\UserRepository;

class BanHandler
{
    private $liteBansRepository;

    private $userRepository;

    public function __construct(LiteBansRepository $liteBansRepository, UserRepository $userRepository)
    {
        $this->liteBansRepository = $liteBansRepository;
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

    private function check(User $user): bool
    {
        return is_null($this->liteBansRepository->findByUser($user));
    }

    public function handle(User $admin, string $name, ?string $reason, ?string $date): void
    {
        $user = $this->getUser($name);
        if (!$this->check($user)) {
            throw new Exception('Этот игрок уже забанен!');
        }

        if (empty(trim($reason))) {
            $reason = 'Без причины';
        } else {
            $reason = strip_tags($reason);
        }

        if (!empty($date)) {
            $until = strtotime($date);

            if ($until <= time()) {
                throw new Exception('Дата должна быть больше чем сейчас!');
            }
        } else {
            $until = -1;
        }

        $ban = new LiteBansBan(
            $admin,
            $user->getUuid(),
            $reason,
            $until
        );

        $this->liteBansRepository->create($ban);

        event(new BanEvent($admin, $user, $ban));
    }
}