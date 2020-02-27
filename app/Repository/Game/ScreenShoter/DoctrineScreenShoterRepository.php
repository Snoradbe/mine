<?php


namespace App\Repository\Game\ScreenShoter;


use App\Repository\PaginatedDoctrineConstructor;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineScreenShoterRepository implements ScreenShoterRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    public function getAll(int $page): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('scren')
            ->orderBy('scren.id', 'DESC')
            ->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function getCountPerDays(string $minDate, string $maxDate, ?string $name = null): array
    {
        if (!empty($name)) {
            $andName = 'username = ? AND';
            $data = [$name, $minDate, $maxDate];
        } else {
            $andName = '';
            $data = [$minDate, $maxDate];
        }

        $q = $this->em->getConnection()->fetchAll(
                "SELECT DATE(`date`) as d, count(*) as c
                 FROM screenshoter_history
                  WHERE $andName DATE(`date`) BETWEEN ? AND ? GROUP BY DATE(`date`)
                  ORDER BY id DESC",
                $data
            );

        $result = [];
        foreach ($q as $data)
        {
            $result[$data['d']] = $data['c'];
        }

        return $result;
    }

    public function getForDate(\DateTime $date, int $page, ?string $name = null): LengthAwarePaginator
    {
        $minDate = $date->format('Y-m-d');
        $date->modify('+1 day');
        $maxDate = $date->format('Y-m-d');

        $q = $this->createQueryBuilder('scren')
            ->where('DATE(scren.date) BETWEEN :min AND :max')
            ->setParameter('min', $minDate)
            ->setParameter('max', $maxDate);

        if (!empty($name)) {
            $q->andWhere('scren.username = :name')
                ->setParameter('name', $name);
        }

        $q->orderBy('scren.id', 'DESC');

        return $this->paginate(
            $q->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }
}