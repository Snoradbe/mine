<?php


namespace App\Listeners\Admin\Servers;


use App\Entity\Site\Group;
use App\Entity\Site\Log;
use App\Events\Admin\Servers\AddServerEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\UserAdminGroup\UserAdminGroupRepository;
use App\Services\Cabinet\CabinetUtils;

class AddServerListener implements Listener
{
    use SiteLogListener;

    private $userAdminGroupRepository;

    private $groupRepository;

    public function __construct(UserAdminGroupRepository $userAdminGroupRepository, GroupRepository $groupRepository)
    {
        $this->userAdminGroupRepository = $userAdminGroupRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param AddServerEvent $event
     */
    public function writeToLogs(AddServerEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            $event->getServer(),
            22,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param AddServerEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);

        $groups = array_map(function (Group $group) {
            return $group->getName();
        }, $this->groupRepository->getAllAdmin(true));
        $admins = $this->userAdminGroupRepository->getAllOnServer(null);

        foreach ($admins as $admin)
        {
            CabinetUtils::getPermissionsManager($event->getServer())
                ->setPrimaryGroup($groups, $admin->getUser()->getUuid(), $admin->getGroup()->getName());
        }
    }
}