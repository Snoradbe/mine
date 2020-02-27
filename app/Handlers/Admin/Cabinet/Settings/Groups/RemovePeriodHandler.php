<?php


namespace App\Handlers\Admin\Cabinet\Settings\Groups;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\Cabinet\Settings\Groups\RemovePeriodEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Cabinet\CabinetSettings;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class RemovePeriodHandler
{
    private $serverRepository;

    private $groupRepository;

    private $settings;

    public function __construct(ServerRepository $serverRepository, GroupRepository $groupRepository, Settings $settings)
    {
        $this->serverRepository = $serverRepository;
        $this->groupRepository = $groupRepository;
        $this->settings = $settings;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    private function getGroup(string $name): Group
    {
        $group = $this->groupRepository->findByName($name);
        if (is_null($group)) {
            throw new Exception('Группа не найдена!');
        }

        return $group;
    }

    public function handle(User $admin, int $serverId, string $group, int $period): void
    {
        $server = $this->getServer($serverId);
        $group = $this->getGroup($group);

        $settings = settings('cabinet', DataType::JSON);

        $groups = CabinetSettings::getGroupsSettings($group->isPrimary());

        $serverGroups = $groups[$server->getId()] ?? [];

        if (!isset($serverGroups[$group->getName()]) || !isset($serverGroups[$group->getName()][$period])) {
            throw new Exception('Такого периода нет!');
        }

        $old = $serverGroups[$group->getName()][$period] ?? -1;

        if ($period == 30) {
            unset($serverGroups[$group->getName()]);
        } else {
            unset($serverGroups[$group->getName()][$period]);
        }

        $groups[$server->getId()] = $serverGroups;

        $settings[$group->isPrimary() ? 'groups' : 'other_groups'] = $groups;

        $this->settings->set('cabinet', $settings);
        $this->settings->save();

        event(new RemovePeriodEvent($admin, $server, $group, $period, $old));
    }
}