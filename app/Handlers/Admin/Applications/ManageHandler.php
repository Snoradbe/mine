<?php


namespace App\Handlers\Admin\Applications;


use App\Entity\Site\Application;
use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
use App\Events\Admin\Applications\ManageEvent;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Repository\Site\Application\ApplicationRepository;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\UserAdminGroup\UserAdminGroupRepository;
use App\Services\Cabinet\CabinetUtils;
use App\Services\Forum\ForumManager;
use App\Services\Permissions\Permissions;

class ManageHandler
{
    private $applicationRepository;

    private $groupRepository;

    private $userAdminGroupRepository;

    public function __construct(
        ApplicationRepository $applicationRepository,
        GroupRepository $groupRepository,
        UserAdminGroupRepository $userAdminGroupRepository)
    {
        $this->applicationRepository = $applicationRepository;
        $this->groupRepository = $groupRepository;
        $this->userAdminGroupRepository = $userAdminGroupRepository;
    }

    private function getApplication(int $id): Application
    {
        $application = $this->applicationRepository->find($id);
        if (is_null($application)) {
            throw new Exception('Заявка не найдена!');
        }

        return $application;
    }

    private function getGroup(string $name): Group
    {
        $group = $this->groupRepository->findByName($name);
        if (is_null($group)) {
            throw new Exception('Группа не найдена!');
        }

        return $group;
    }

    private function getPrimaryGroups(): array
    {
        return array_map(function (Group $group) {
            return $group->getName();
        }, $this->groupRepository->getAllAdmin(true));
    }

    public function handle(User $admin, int $id, int $type): void
    {
        $application = $this->getApplication($id);
        if ($application->getStatus() != Application::WAIT) {
            throw new Exception('Эта заявка уже обработана!');
        }

        $group = $this->getGroup($application->getPosition());

        $canManage = $admin->permissions()->hasPermission(Permissions::MP_APPLICATIONS_MANAGE_ALL);
        if (
            !$canManage
            &&
            !is_null($admin->permissions()->getServersWithPermission(Permissions::MP_APPLICATIONS_MANAGE))
            &&
            !in_array($application->getServer(), $admin->permissions()->getServersWithPermission(Permissions::MP_APPLICATIONS_MANAGE))
        ) {
            throw new PermissionDeniedException();
        }

        $application->setStatus($type);
        $this->applicationRepository->update($application);

        if ($type == Application::ACCEPT) {
            $this->userAdminGroupRepository->create(new UserAdminGroup(
                $application->getUser(),
                $application->getServer(),
                $group
            ));

            CabinetUtils::getPermissionsManager($application->getServer())
                ->setPrimaryGroup($this->getPrimaryGroups(), $application->getUser()->getUuid(), $application->getPosition());

            if (!is_null($group->getForumId())) {
                $member = ForumManager::getMember($application->getUser());
                if (!is_null($member)) {
                    $member->setGroupId($group->getForumId());
                    $member->setTitle(ucfirst($group->getName()) . ' / ' . $application->getServer()->getName());

                    ForumManager::updateMember($member);
                }
            }
        }

        event(new ManageEvent($admin, $application, $type));
    }
}