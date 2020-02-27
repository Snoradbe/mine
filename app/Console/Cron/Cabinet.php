<?php


namespace App\Console\Cron;


use App\Entity\Site\UserGroup;
use App\Entity\Site\UserNotification;
use App\Repository\Site\UserGroup\UserGroupRepository;
use App\Repository\Site\UserNotification\UserNotificationRepository;
use App\Services\Cabinet\CabinetUtils;
use App\Services\Cabinet\Cloak\Image;
use App\Services\Permissions\Permissions;
use App\Services\Skills\Skills;
use Illuminate\Console\Command;

class Cabinet extends Command
{
    protected $signature = 'rm:cron:cabinet';

    private $userGroupRepository;

    private $userNotificationRepository;

    private $deletedCloaks = [];

    private $deletedPrefixes = [];

    private $deletedGroups = [];

    public function __construct(UserGroupRepository $userGroupRepository, UserNotificationRepository $userNotificationRepository)
    {
        parent::__construct();

        $this->userGroupRepository = $userGroupRepository;
        $this->userNotificationRepository = $userNotificationRepository;
    }

    public function handle()
    {
        $expiredGroups = $this->userGroupRepository->getExpiredGroups();

        foreach ($expiredGroups as $userGroup)
        {
            $this->delCloak($userGroup);
            $this->delPrefix($userGroup);
            $this->delGroup($userGroup);

            $this->log($userGroup);

            $this->userGroupRepository->delete($userGroup);
        }
    }

    private function delCloak(UserGroup $userGroup): void
    {
        if (Skills::hasCloakSkill($userGroup->getUser())) {
            return;
        }

        $file = Image::getAbsolutePath($userGroup->getUser()->getName());
        if (is_file($file)) {
            @unlink($file);
            if (!in_array($userGroup->getUser()->getName(), $this->deletedCloaks)) {
                $this->deletedCloaks[] = $userGroup->getUser()->getName();
            }
        }
    }

    private function delPrefix(UserGroup $userGroup): void
    {
        if (!$userGroup->getGroup()->hasPermission(Permissions::CABINET_PREFIX)) {
            return;
        }

        CabinetUtils::getPermissionsManager($userGroup->getServer())
            ->removePrefixSuffix($userGroup->getUser()->getUuid());

        if (!in_array($userGroup->getUser()->getName(), $this->deletedPrefixes)) {
            $this->deletedPrefixes[] = $userGroup->getUser()->getName();
        }
    }

    private function delGroup(UserGroup $userGroup): void
    {
        if ($userGroup->getGroup()->isPrimary()) {
            CabinetUtils::getPermissionsManager($userGroup->getServer())
                ->removeGroup($userGroup->getUser()->getUuid(), $userGroup->getGroup()->getName());
        } else {
            CabinetUtils::getPermissionsManager($userGroup->getServer())
                ->removePermission($userGroup->getUser()->getUuid(), $userGroup->getGroup()->getPermissionName());
        }

        if (!isset($this->deletedGroups[$userGroup->getUser()->getName()])) {
            $this->deletedGroups[$userGroup->getUser()->getName()] = [];
        }

        $this->deletedGroups[$userGroup->getUser()->getName()] = [
            'server' => $userGroup->getServer()->getName(),
            'group' => $userGroup->getGroup()->getName()
        ];
    }

    private function log(UserGroup $userGroup): void
    {
        //TODO: to discord
        $cloak = in_array($userGroup->getUser()->getName(), $this->deletedCloaks) ? 'Плащ был удален.' : '';
        $prefix = in_array($userGroup->getUser()->getName(), $this->deletedPrefixes) ? 'Префикс был удален.' : '';

        $this->userNotificationRepository->create(new UserNotification(
            $userGroup->getUser(),
            sprintf(
                'Срок вашей группы %s на сервере %s истек. Группа была удалена. %s %s',
                strtoupper($userGroup->getGroup()->getName()),
                $userGroup->getServer()->getName(),
                $cloak,
                $prefix
            )
        ));
    }
}