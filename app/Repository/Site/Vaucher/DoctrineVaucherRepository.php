<?php


namespace App\Repository\Site\Vaucher;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Repository\PaginatedDoctrineConstructor;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineVaucherRepository implements VaucherRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    public function getAll(?User $user, bool $onlyActive = true, int $page = 1): LengthAwarePaginator
    {
        $query = $this->createQueryBuilder('vaucher');

        if (!is_null($user)) {
            $query->where('vaucher.user = :user')
                ->setParameter('user', $user);
        }

        if ($onlyActive) {
            $query->andWhere('(vaucher.amount IS NULL OR vaucher.amount > 0) AND (vaucher.end IS NULL OR vaucher.end > :now)')
                ->setParameter('now', date('Y-m-d H:i:s'));
        }

        return $this->paginate($query->getQuery(), static::PER_PAGE, $page, false);
    }

    public function find(int $id): ?Vaucher
    {
        return $this->er->find($id);
    }

    public function findByCode(string $code): ?Vaucher
    {
        return $this->er->findOneBy(['code' => $code]);
    }

    public function create(Vaucher $vaucher): void
    {
        $this->em->persist($vaucher);
        $this->em->flush();
    }

    public function update(Vaucher $vaucher): void
    {
        $this->em->flush($vaucher);
    }

    public function delete(Vaucher $vaucher): void
    {
        $this->em->remove($vaucher);
        $this->em->flush();
    }
}