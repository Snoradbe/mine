<?php


namespace App\Repository\Site\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Repository\PaginatedDoctrineConstructor;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineBugReportRepository implements BugReportRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    public function getAll(int $page): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('bug')->orderBy('bug.id', 'DESC')->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function getAllUser(User $user, int $page): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('bug')
            ->where('bug.user = :user')
            ->setParameter(':user', $user)
            ->orderBy('bug.id', 'DESC')
            ->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function find(int $id): ?BugReport
    {
        return $this->er->find($id);
    }

    public function getCountToday(User $user): int
    {
        return (int)$this->em->createQuery(
            sprintf(
                'SELECT count(bug.id) as c
                        FROM %s bug
                        WHERE bug.user = %d AND bug.createdAt >= %s',
                BugReport::class,
                $user->getId(),
                date('Y-m-d')
            )
        )
            ->getSingleScalarResult();
    }

    public function getCountWait(?array $servers): int
    {
        if (empty($servers)) {
            return (int)$this->em->createQuery(
                sprintf(
                    'SELECT count(bug.id) as c
                        FROM %s bug
                        WHERE bug.status = %d',
                    BugReport::class,
                    BugReport::IS_ACTIVE['type']
                )
            )
                ->getSingleScalarResult();
        }

        return (int)$this->em->createQuery(
            sprintf(
                'SELECT count(bug.id) as c
                        FROM %s bug
                        WHERE bug.status = %d
                        AND bug.server IN (:servers)',
                BugReport::class,
                BugReport::IS_ACTIVE['type']
            )
        )
            ->setParameter('servers', $servers)
            ->getSingleScalarResult();
    }

    public function create(BugReport $bugReport): void
    {
        $this->em->persist($bugReport);
        $this->em->flush();
    }

    public function update(BugReport $bugReport): void
    {
        $this->em->flush($bugReport);
    }

    public function delete(BugReport $bugReport): void
    {
        $this->em->remove($bugReport);
        $this->em->flush();
    }
}