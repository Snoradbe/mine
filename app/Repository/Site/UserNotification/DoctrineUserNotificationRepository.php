<?php


namespace App\Repository\Site\UserNotification;


use App\Entity\Site\User;
use App\Entity\Site\UserNotification;
use App\Repository\DoctrineClearCache;
use App\Repository\PaginatedDoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineUserNotificationRepository implements UserNotificationRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams, DoctrineClearCache;

    private const ALL_PER_PAGE = 30;

    private const USER_LIMIT = 10;

    private const USER_DAY_EXPIRE = 604800; //В течении скольких дней показывать уведомления (7 * 86400 = 604800)

    /**
     * @param User|null $user
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getAll(?User $user, int $page): LengthAwarePaginator
    {
        $query = $this->createQueryBuilder('un')
            ->orderBy('un.id', 'DESC');

        if (!is_null($user)) {
            $query->where('un.user = :user')
                ->setParameter('user', $user);
        }

        return $this->paginate(
            $query->getQuery(),
            static::ALL_PER_PAGE,
            $page,
            false
        );
    }

    /**
     * @param User $user
     * @return UserNotification[]
     */
    public function getAllByUser(User $user): array
    {
        return $this->createQueryBuilder('un')
            ->where('un.user = :user AND un.date >= :date')
            ->setParameter('user', $user)
            ->setParameter('date', date('Y-m-d H:i', time() - static::USER_DAY_EXPIRE))
            ->orderBy('un.isRead', 'ASC')
            ->addOrderBy('un.id', 'DESC')
            ->setMaxResults(static::USER_LIMIT)
            ->getQuery()
            //->useResultCache(true, 600)
            ->getResult();
    }

    /**
     * @param UserNotification $notification
     */
    public function create(UserNotification $notification): void
    {
        $this->em->persist($notification);
        $this->em->flush();
    }

    /**
     * @param UserNotification $notification
     */
    public function update(UserNotification $notification): void
    {
        $this->clearResultCache();
        $this->em->flush($notification);
    }

    /**
     * @param User $user
     */
    public function markReadAllForUser(User $user): void
    {
        $this->clearResultCache();

        $this->createQueryBuilder('un')
            ->update()
            ->set('un.isRead', 1)
            ->where('un.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
}