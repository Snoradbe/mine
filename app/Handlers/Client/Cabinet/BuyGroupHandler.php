<?php


namespace App\Handlers\Client\Cabinet;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserGroup;
use App\Events\Client\Cabinet\BuyGroupEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\User\UserRepository;
use App\Services\Cabinet\Groups\OtherGroup;
use App\Services\Cabinet\Groups\PrimaryGroup;
use App\Services\Discounts\Discounts;

class BuyGroupHandler
{
    private $userRepository;

    private $serverRepository;

    private $groupRepository;

    public function __construct(
        UserRepository $userRepository,
        ServerRepository $serverRepository,
        GroupRepository $groupRepository)
    {
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
        $this->groupRepository = $groupRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    private function getGroup(string $groupName): Group
    {
        $group = $this->groupRepository->findByName($groupName);
        if (is_null($group)) {
            throw new Exception('Группа не найдена!');
        }

        return $group;
    }

    private function buyPrimary(User $user, Server $server, Group $group, int $period): UserGroup
    {
        $giver = app()->make(PrimaryGroup::class);
        return $giver->give($user, $server, $group, $period);
    }

    private function buyOther(User $user, Server $server, Group $group, int $period): UserGroup
    {
        $giver = app()->make(OtherGroup::class);
        return $giver->give($user, $server, $group, $period);
    }

    public function handle(User $user, int $serverId, string $groupName, int $period, int &$price): UserGroup
    {
        $server = $this->getServer($serverId);
        $group = $this->getGroup($groupName);

        if ($group->isAdmin()) {
            throw new Exception('...');
        }

        $discount = Discounts::getInstance()->getDiscount($server, 'cabinet', 'group', $group);

        $price = Discounts::getPriceWithDiscount($price, $discount);

        $user->withdrawMoney($price);

        if ($group->isPrimary()) {
            $uGroup = $this->buyPrimary($user, $server, $group, $period);
        } else {
            $uGroup = $this->buyOther($user, $server, $group, $period);
        }

        $this->userRepository->update($user);

        event(new BuyGroupEvent($user, $server, $uGroup, $period, $price));

        return $uGroup;
    }
}