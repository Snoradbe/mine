<?php


namespace App\Repository\Site\Application;


use App\Entity\Site\Application;
use App\Entity\Site\User;
use App\Repository\PaginatedDoctrineConstructor;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineApplicationRepository implements ApplicationRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    public function getAll(?int $status, ?array $servers, int $page): LengthAwarePaginator
    {
        $query =
            !is_null($status)
                ?
                $this->createQueryBuilder('app')
                    ->where('app.status = :status')
                    ->setParameter('status', $status)
                    ->orderBy('app.id', 'DESC')
                :
                $this->createQueryBuilder('app')->orderBy('app.id', 'DESC');

        if (is_array($servers)) {
            $query->andWhere('app.server IN (:servers)')
                ->setParameter('servers', $servers);
        }

        return $this->paginate($query->getQuery(), static::PER_PAGE, $page, false);
    }

    public function search(string $name, ?array $servers, int $page, ?int $status = null): LengthAwarePaginator
    {
        $query = $this->createQueryBuilder('app')
            ->select('app', 'user')
            ->join('app.user', 'user')
            ->where('user.name LIKE :name')
            ->setParameter('name', "%$name%");

        if (!is_null($status)) {
            $query->andWhere('app.status = :status')
                ->setParameter('status', $status);
        }

        if (is_array($servers)) {
            $query->andWhere('app.server IN (:servers)')
                ->setParameter('servers', $servers);
        }

        $query->orderBy('app.id', 'DESC');

        $query = $query->getQuery();

        return $this->paginate($query, static::PER_PAGE, $page, false);
    }

    public function find(int $id): ?Application
    {
        return $this->er->find($id);
    }

    public function findLast(User $user): ?Application
    {
        return $this->createQueryBuilder('app')
            ->select('app', 'user')
            ->join('app.user', 'user')
            ->where('user = :user')
            ->setParameter('user', $user)
            ->orderBy('app.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getCountWait(?array $servers): int
    {
        if (empty($servers)) {
            return (int)$this->em->createQuery(
                sprintf(
                    'SELECT count(app.id) as c
                        FROM %s app
                        WHERE app.status = %d',
                    Application::class,
                    Application::WAIT
                )
            )
                ->getSingleScalarResult();
        }

        return (int)$this->em->createQuery(
            sprintf(
                'SELECT count(app.id) as c
                        FROM %s app
                        WHERE app.status = %d
                        AND app.server IN (:servers)',
                Application::class,
                Application::WAIT
            )
        )
            ->setParameter('servers', $servers)
            ->getSingleScalarResult();
    }

    public function create(Application $application): void
    {
        $this->em->persist($application);
        $this->em->flush();
    }

    public function update(Application $application): void
    {
        $this->em->flush($application);
    }

    public function delete(Application $application): void
    {
        $this->em->remove($application);
        $this->em->flush();
    }
}