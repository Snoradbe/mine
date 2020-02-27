<?php


namespace App\Repository\Site\Shop\Statistic;


use App\Entity\Site\Shop\Statistic;
use App\Repository\PaginatedDoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineStatisticRepository implements StatisticRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    private const PER_PAGE = 30;

    public function find(int $id): ?Statistic
    {
        return $this->er->find($id);
    }

    public function getAll(int $page): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('statistic')->orderBy('statistic.id', 'DESC')->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function countBuysForTime(?string $date = null): int
    {
        $sql = 'SELECT count(s) FROM %s s';
        if (!empty($date)) {
            $sql .= ' WHERE s.dayDate = :date';
        }

        $query = $this->em->createQuery(
            sprintf(
                $sql,
                Statistic::class
            )
        );

        if (!empty($date)) {
            $query->setParameter('date', $date);
        }

        return (int) $query->getSingleScalarResult();
    }

    public function sumBuysForTime(string $valute, ?string $date = null): int
    {
        $sql = 'SELECT sum(s.price) FROM %s s';
        $where = '';
        if (!empty($date)) {
            $where .= 'AND s.dayDate = :date ';
        }

        $where .= 'AND s.valute = :valute ';

        $sql .= ' WHERE' . substr($where, 3);

        $query = $this->em->createQuery(
            sprintf(
                $sql,
                Statistic::class
            )
        );

        if (!empty($date)) {
            $query->setParameter('date', $date);
        }

        $query->setParameter('valute', $valute);

        return (int) $query->getSingleScalarResult();
    }

    public function chartForMonth(int $year, int $month): array
    {
        return $this->em->createQuery(
            sprintf(
            /* @lang text */
                'SELECT s.dayDate as day, count(s) as total
                        FROM %s s
                        WHERE 
                        YEAR(s.createdAt) = :year
                        AND MONTH(s.createdAt) = :month
                        GROUP BY day
                        ORDER BY day',
                Statistic::class
            )
        )
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->getResult();
    }

    public function chartForDay(int $year, int $month, int $day): array
    {
        return $this->em->createQuery(
            sprintf(
            /* @lang text */
                'SELECT HOUR(s.createdAt) as hour, count(s) as total
                        FROM %s s
                        WHERE 
                        YEAR(s.createdAt) = :year
                        AND MONTH(s.createdAt) = :month
                        AND DAY(s.createdAt) = :day
                        GROUP BY hour
                        ORDER BY hour',
                Statistic::class
            )
        )
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->setParameter('day', $day)
            ->getResult();
    }

    public function create(Statistic $statistic, bool $flush = true): EntityManagerInterface
    {
        $this->em->persist($statistic);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }

    public function delete(Statistic $statistic, bool $flush = true): EntityManagerInterface
    {
        $this->em->remove($statistic);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }
}