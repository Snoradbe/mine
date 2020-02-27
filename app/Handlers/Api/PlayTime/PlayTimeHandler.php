<?php


namespace App\Handlers\Api\PlayTime;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Api\User\LevelUpEvent;
use App\Exceptions\Exception;
use App\Helpers\LevelHelper;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\User\UserRepository;

class PlayTimeHandler
{
    private $userRepository;

    private $serverRepository;

    public function __construct(UserRepository $userRepository, ServerRepository $serverRepository)
    {
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
    }

    private function getUser(string $name): User
    {
        $user = $this->userRepository->findByName($name);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    /**
     * @param string $userName
     * @param int $serverId
     * @param int $minutes - количество реально отыграных минут
     * @param int $exp - количесво минут с различными множителями
     * @throws Exception
     */
    public function handle(string $userName, int $serverId, int $minutes = 10, int $exp = 10): void
    {
        $user = $this->getUser($userName);
        $server = $this->getServer($serverId);

        //Если игрок сразу на двух серверах онлайн, то засчитываем только 1
        if ($user->getLastServer() !== $server->getId()) {
            return;
        }

        $userExp = $user->getExp() + $exp;

        $nextLevelExp = LevelHelper::getNextLevelExp($user->getLevel());
        if ($userExp >= $nextLevelExp) {
            //level up
            $oldLevel = $user->getLevel();
            $user->addLevel();
            $user->setExp($userExp - $nextLevelExp);
            $user->addSkillPoints(1);
            event(new LevelUpEvent($user, $oldLevel, $user->getLevel()));
        } else {
            $user->addExp($exp);
        }

        $user->addOnlineTime($minutes);
        $user->addOnlineTimeTotal($minutes);
        $this->userRepository->update($user);
    }
}