<?php


namespace App\Repository\Site\VaucherUser;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Entity\Site\VaucherUser;
use App\Repository\PaginatedDoctrineConstructor;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineVaucherUserRepository implements VaucherUserRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    public function getAll(int $page = 1): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('vauchuser')
            ->orderBy('vauchuser.id', 'DESC')
            ->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function getByUser(User $user, int $page = 1): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('vauchuser')
                ->where('vauchuser.user = :user')
                ->orderBy('vauchuser.id', 'DESC')
                ->setParameter('user', $user)
                ->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function getByVaucher(Vaucher $vaucher, int $page = 1): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('vauchuser')
                ->where('vauchuser.vaucher = :vaucher')
                ->orderBy('vauchuser.id', 'DESC')
                ->setParameter('vaucher', $vaucher)
                ->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function find(int $id): ?VaucherUser
    {
        return $this->er->find($id);
    }

    public function findByUserVaucher(User $user, Vaucher $vaucher): ?VaucherUser
    {
        return $this->er->findOneBy(['user' => $user, 'vaucher' => $vaucher]);
    }

    public function create(VaucherUser $vaucherUser): void
    {
        $this->em->persist($vaucherUser);
        $this->em->flush();
    }

    public function delete(VaucherUser $vaucherUser): void
    {
        $this->em->remove($vaucherUser);
        $this->em->flush();
    }
}