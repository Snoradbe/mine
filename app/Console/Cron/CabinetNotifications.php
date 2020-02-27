<?php


namespace App\Console\Cron;


use App\Entity\Site\UserNotification;
use App\Repository\Site\UserGroup\UserGroupRepository;
use App\Repository\Site\UserNotification\UserNotificationRepository;
use Illuminate\Console\Command;

class CabinetNotifications extends Command
{
    protected $signature = 'rm:cron:cabinet:notifications';

    private $userGroupRepository;

    private $userNotificationRepository;

    private const DAYS = 3;

    public function __construct(UserGroupRepository $userGroupRepository, UserNotificationRepository $userNotificationRepository)
    {
        parent::__construct();

        $this->userGroupRepository = $userGroupRepository;
        $this->userNotificationRepository = $userNotificationRepository;
    }

    public function handle()
    {
        $userGroups = $this->userGroupRepository->getPreExpiredGroups(static::DAYS);
        foreach ($userGroups as $userGroup)
        {
            $this->userNotificationRepository->create(new UserNotification(
                $userGroup->getUser(),
                sprintf(
                    'Срок вашей группы %s на сервере %s истекает через %d дня. Не забудьте ее продлить.',
                    strtoupper($userGroup->getGroup()->getName()),
                    $userGroup->getServer()->getName(),
                    static::DAYS
                )
            ));
        }
    }
}