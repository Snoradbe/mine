<?php


namespace App\Repository\Site\Log;


use App\Entity\Site\Log;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Repository\PaginatedDoctrineConstructor;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineLogRepository implements LogRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    private const PER_PAGE = 20;

    public function getAll(?Server $server, ?string $user, ?array $types, int $page): LengthAwarePaginator
    {
        $query = $this->createQueryBuilder('log')
            ->select('log', 'user')
            ->join('log.user', 'user');

        if (!is_null($server)) {
            $query->andWhere('log.server = :server')
                ->setParameter('server', $server);
        }

        if (!empty($user)) {
            $query->andWhere('user.name LIKE :user')
                ->setParameter('user', "$user%");
        }

        if (!empty($types)) {
            $query->andWhere('log.type IN (:types)')
                ->setParameter('types', $types);
        }

        $query->orderBy('log.id', 'DESC');

        return $this->paginate(
            $query->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function getAllClient(?Server $server, User $user, array $types, int $type, int $page): LengthAwarePaginator
    {
        $query = $this->createQueryBuilder('log')
            ->where('log.user = :user')
            ->andWhere('log.type IN (:types)')
            ->setParameter('types', $types)
            ->setParameter('user', $user);

        if (!is_null($server)) {
            $query->andWhere('(log.server IS NULL OR log.server = :server)')
                ->setParameter('server', $server);
        }

        switch ($type)
        {
            case static::CLIENT_SPENT_TYPE:
                $query->andWhere('log.spent IS NOT NULL'); break;

            case static::CLIENT_RECEIVED_TYPE:
                $query->andWhere('log.received IS NOT NULL'); break;
        }

        $query->orderBy('log.id', 'DESC');

        return $this->paginate(
            $query->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function findByUser(User $user, array $types = [], int $page = 1): LengthAwarePaginator
    {
        $query = $this->createQueryBuilder('log')
            ->where('log.user = :user')
            ->setParameter('user', $user)
            ->orderBy('log.id', 'DESC');

        if (!empty($types)) {
            $query->andWhere('log.type IN (:types)')
                ->setParameter('types', $types);
        }

        return $this->paginate(
            $query->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function create(Log $log): void
    {
        $this->em->persist($log);
        $this->em->flush();
    }
}