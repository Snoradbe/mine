<?php


namespace App\Repository\Game\Shop\RealMine;


use App\Entity\Game\Shop\RealMine;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Repository\PaginatedDoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineRealMineRepository implements RealMineRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    private const PER_PAGE = 5;

    public function find(int $id): ?RealMine
    {
        return $this->er->find($id);
    }

    public function getAll(Server $server, int $page): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('rm')
                ->where('rm.server = :server')
                ->setParameter('server', $server)
                ->orderBy('rm.id', 'DESC')
                ->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function getAllByUser(User $user, Server $server, int $page): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('rm')
                ->where('rm.user = :user AND rm.server = :server')
                ->setParameter('user', $user)
                ->setParameter('server', $server)
                ->orderBy('rm.id', 'DESC')
                ->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function create(RealMine $realMine, bool $flush = true): EntityManagerInterface
    {
        $this->em->persist($realMine);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }

    public function update(RealMine $realMine, bool $flush = true): EntityManagerInterface
    {
        if ($flush) {
            $this->em->flush($realMine);
        }

        return $this->em;
    }

    public function delete(RealMine $realMine, bool $flush = true): EntityManagerInterface
    {
        $this->em->remove($realMine);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }
}