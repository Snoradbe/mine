<?php


namespace App\Handlers\Admin\Banlist;


use App\Entity\Game\LiteBans\LiteBansBan;
use App\Entity\Site\User;
use App\Events\Admin\Banlist\UnbanEvent;
use App\Exceptions\Exception;
use App\Repository\Game\LiteBans\LiteBansRepository;
use App\Repository\Site\User\UserRepository;

class UnbanHandler
{
    private $liteBansRepository;

    private $userRepository;

    public function __construct(LiteBansRepository $liteBansRepository, UserRepository $userRepository)
    {
        $this->liteBansRepository = $liteBansRepository;
        $this->userRepository = $userRepository;
    }

    private function getBan(int $id): LiteBansBan
    {
        $ban = $this->liteBansRepository->find($id);
        if (is_null($ban) || !$ban->isActive()) {
            throw new Exception('Бан не найден, либо время бана истекло!');
        }

        return $ban;
    }

    private function getUser(string $name): User
    {
        $user = $this->userRepository->findByName($name);
        if (is_null($user)) {
            throw new Exception("Игрок $name не найден в списке пользователей!");
        }

        return $user;
    }

    public function handle(User $admin, int $id)
    {
        $ban = $this->getBan($id);
        $user = $this->getUser($ban->getName());

        $ban->unban($admin);
        $this->liteBansRepository->update($ban);

        event(new UnbanEvent($admin, $user, $ban));
    }
}