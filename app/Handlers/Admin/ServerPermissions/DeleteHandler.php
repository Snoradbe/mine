<?php


namespace App\Handlers\Admin\ServerPermissions;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\ServerPermissions\DeletePermissionEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Cabinet\CabinetUtils;

class DeleteHandler
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

    private function getGroup(string $name): Group
    {
        $group = $this->groupRepository->findByName($name);
        if (is_null($group)) {
            throw new Exception('Группа не найдена!');
        }

        return $group;
    }

    private function delete(Server $server, Group $group, array $permissions)
    {
        $pm = CabinetUtils::getPermissionsManager($server);

        $pm->removePermissionsFromGroup($group->getName(), $permissions);
    }

    public function handle(User $admin, int $serverId, string $group, array $permissions, bool $fromAll): void
    {
        $server = $this->getServer($serverId);
        $group = $this->getGroup($group);

        if ($fromAll) {
            foreach ($this->serverRepository->getAll(false) as $serv)
            {
                $this->delete($serv, $group, $permissions);
            }
        } else {
            $this->delete($server, $group, $permissions);
        }

        event(new DeletePermissionEvent($admin, $fromAll ? null : $server, $group, $permissions));
    }
}