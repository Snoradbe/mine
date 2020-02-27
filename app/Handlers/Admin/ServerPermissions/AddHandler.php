<?php


namespace App\Handlers\Admin\ServerPermissions;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\ServerPermissions\AddPermissionEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Cabinet\CabinetUtils;

class AddHandler
{
    private $serverRepository;

    private $groupRepository;

    public function __construct(ServerRepository $serverRepository, GroupRepository $groupRepository)
    {
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

    private function getGroups(string $groups): array
    {
        $result = [];
        $list = explode(',', $groups);
        $serverGroups = $this->groupRepository->getAll();

        foreach ($list as $group)
        {
            $checked = false;
            foreach ($serverGroups as $serverGroup)
            {
                if ($group == $serverGroup->getName()) {
                    $checked = true;
                    $result[] = $serverGroup->getName();
                }
            }
            if (!$checked) {
                throw new Exception("Группа " . $group . ' не найдена!');
            }
        }

        return $result;
    }

    private function addToServer(Server $server, array $groups, array $permissions): array
    {
        $pm = CabinetUtils::getPermissionsManager($server);

        $result = [];

        foreach ($groups as $group)
        {
            $result[$group] = $pm->addPermissionsToGroup($group, $permissions);
        }

        return $result;
    }

    public function handle(User $admin, int $serverId, string $groups, string $permissions, bool $onAll): array
    {
        $server = $this->getServer($serverId);
        $groups = $this->getGroups($groups);
        $permissions = explode(',', str_replace(' ', '', $permissions));

        $result = [];

        if ($onAll) {
            foreach ($this->serverRepository->getAll(false) as $serv)
            {
                $result[$serv->getId()] = $this->addToServer($serv, $groups, $permissions);
            }
        } else {
            $result = $this->addToServer($server, $groups, $permissions);
        }

        event(new AddPermissionEvent($admin, $onAll ? null : $server, $groups, $permissions));

        return $result;
    }
}