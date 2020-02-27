<?php


namespace App\Providers;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register(): void
    {
        $repositories = [
            \App\Repository\Site\User\UserRepository::class => [
                'concrete' => \App\Repository\Site\User\DoctrineUserRepository::class,
                'entity' => \App\Entity\Site\User::class
            ],
            \App\Repository\Site\Server\ServerRepository::class => [
                'concrete' => \App\Repository\Site\Server\DoctrineServerRepository::class,
                'entity' => \App\Entity\Site\Server::class
            ],
            \App\Repository\Site\Group\GroupRepository::class => [
                'concrete' => \App\Repository\Site\Group\DoctrineGroupRepository::class,
                'entity' => \App\Entity\Site\Group::class
            ],
            \App\Repository\Site\UserGroup\UserGroupRepository::class => [
                'concrete' => \App\Repository\Site\UserGroup\DoctrineUserGroupRepository::class,
                'entity' => \App\Entity\Site\UserGroup::class
            ],
            \App\Repository\Site\UserAdminGroup\UserAdminGroupRepository::class => [
                'concrete' => \App\Repository\Site\UserAdminGroup\DoctrineUserAdminGroupRepository::class,
                'entity' => \App\Entity\Site\UserAdminGroup::class
            ],
            \App\Repository\Site\Settings\SettingsRepository::class => [
                'concrete' => \App\Repository\Site\Settings\DoctrineSettingsRepository::class,
                'entity' => \App\Entity\Site\Setting::class
            ],
            \App\Repository\Site\Vaucher\VaucherRepository::class => [
                'concrete' => \App\Repository\Site\Vaucher\DoctrineVaucherRepository::class,
                'entity' => \App\Entity\Site\Vaucher::class
            ],
            \App\Repository\Site\VaucherUser\VaucherUserRepository::class => [
                'concrete' => \App\Repository\Site\VaucherUser\DoctrineVaucherUserRepository::class,
                'entity' => \App\Entity\Site\VaucherUser::class
            ],
            \App\Repository\Site\Application\ApplicationRepository::class => [
                'concrete' => \App\Repository\Site\Application\DoctrineApplicationRepository::class,
                'entity' => \App\Entity\Site\Application::class
            ],
            \App\Repository\Site\BugReport\BugReportRepository::class => [
                'concrete' => \App\Repository\Site\BugReport\DoctrineBugReportRepository::class,
                'entity' => \App\Entity\Site\BugReport::class
            ],
            \App\Repository\Site\BugReportMessage\BugReportMessageRepository::class => [
                'concrete' => \App\Repository\Site\BugReportMessage\DoctrineBugReportMessageRepository::class,
                'entity' => \App\Entity\Site\BugReportMessage::class
            ],
            \App\Repository\Site\Log\LogRepository::class => [
                'concrete' => \App\Repository\Site\Log\DoctrineLogRepository::class,
                'entity' => \App\Entity\Site\Log::class
            ],


            \App\Repository\Game\LiteBans\LiteBansRepository::class => [
                'concrete' =>  \App\Repository\Game\LiteBans\DoctrineLiteBansRepository::class,
                'entity' => \App\Entity\Game\LiteBans\LiteBansBan::class
            ],


            \App\Repository\Site\Shop\Category\CategoryRepository::class => [
                'concrete' =>  \App\Repository\Site\Shop\Category\DoctrineCategoryRepository::class,
                'entity' => \App\Entity\Site\Shop\Category::class
            ],
            \App\Repository\Site\Shop\Item\ItemRepository::class => [
                'concrete' =>  \App\Repository\Site\Shop\Item\DoctrineItemRepository::class,
                'entity' => \App\Entity\Site\Shop\Item::class
            ],
            \App\Repository\Site\Shop\ItemType\ItemTypeRepository::class => [
                'concrete' =>  \App\Repository\Site\Shop\ItemType\DoctrineItemTypeRepository::class,
                'entity' => \App\Entity\Site\Shop\ItemType::class
            ],
            \App\Repository\Site\Shop\Packet\PacketRepository::class => [
                'concrete' =>  \App\Repository\Site\Shop\Packet\DoctrinePacketRepository::class,
                'entity' => \App\Entity\Site\Shop\Packet::class
            ],
            \App\Repository\Site\Shop\Product\ProductRepository::class => [
                'concrete' =>  \App\Repository\Site\Shop\Product\DoctrineProductRepository::class,
                'entity' => \App\Entity\Site\Shop\Product::class
            ],
            \App\Repository\Site\Shop\Statistic\StatisticRepository::class => [
                'concrete' =>  \App\Repository\Site\Shop\Statistic\DoctrineStatisticRepository::class,
                'entity' => \App\Entity\Site\Shop\Statistic::class
            ],
            \App\Repository\Game\Shop\RealMine\RealMineRepository::class => [
                'concrete' =>  \App\Repository\Game\Shop\RealMine\DoctrineRealMineRepository::class,
                'entity' => \App\Entity\Game\Shop\RealMine::class
            ],

            \App\Repository\Site\UserNotification\UserNotificationRepository::class => [
                'concrete' =>  \App\Repository\Site\UserNotification\DoctrineUserNotificationRepository::class,
                'entity' => \App\Entity\Site\UserNotification::class
            ],

            \App\Repository\Site\VoteLog\VoteLogRepository::class => [
                'concrete' =>  \App\Repository\Site\VoteLog\DoctrineVoteLogRepository::class,
                'entity' => \App\Entity\Site\VoteLog::class
            ],

            \App\Repository\Site\Discount\DiscountRepository::class => [
                'concrete' =>  \App\Repository\Site\Discount\DoctrineDiscountRepository::class,
                'entity' => \App\Entity\Site\Discount::class
            ],

            \App\Repository\Site\Skills\SkillsRepository::class => [
                'concrete' =>  \App\Repository\Site\Skills\DoctrineSkillsRepository::class,
                'entity' => \App\Entity\Site\Skill::class
            ],

            \App\Repository\Site\UserSkill\UserSkillRepository::class => [
                'concrete' =>  \App\Repository\Site\UserSkill\DoctrineUserSkillRepository::class,
                'entity' => \App\Entity\Site\UserSkill::class
            ],

            \App\Repository\Site\TopLastVotes\TopLastVotesRepository::class => [
                'concrete' =>  \App\Repository\Site\TopLastVotes\DoctrineTopLastVotesRepository::class,
                'entity' => \App\Entity\Site\TopLastVotes::class
            ],








            /*MemberRepository::class => [
                'concrete' => DoctrineMemberRepository::class,
                'entity' => \App\Entity\Forum\ForumUser::class
            ],*/
        ];

        foreach ($repositories as $interface => $data) {
            $this->app->when($data['concrete'])
                ->needs(EntityRepository::class)
                ->give(function () use ($data) {
                    return $this->buildEntityRepository($data['entity']);
                });
            $this->app->singleton($interface, $data['concrete']);
        }
    }

    private function buildEntityRepository(string $entity)
    {
        return new EntityRepository(
            $this->app->make(EntityManagerInterface::class),
            new ClassMetadata($entity)
        );
    }
}