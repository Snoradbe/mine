<?php


namespace App\Repository\Site\UserGroup;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserGroup;
use App\Repository\DoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserGroupRepository implements UserGroupRepository
{
    use DoctrineConstructor;

    public function getAll(): array
    {
        return $this->er->findAll();
    }

    public function find(int $id): ?UserGroup
    {
        return $this->er->find($id);
    }

    public function findByUser(User $user): array
    {
        return $this->er->createQueryBuilder('usergroup')
            ->where('usergroup.user = :u')
            ->setParameter('u', $user)
            ->getQuery()
            ->getResult();
    }

    public function findByServer(Server $server): array
    {
        return $this->er->createQueryBuilder('usergroup')
            ->where('usergroup.server = :s')
            ->setParameter('s', $server)
            ->getQuery()
            ->getResult();
    }

    public function findByUserServer(User $user, Server $server): array
    {
        return $this->er->createQueryBuilder('usergroup')
            ->where('usergroup.user = :u AND usergroup.server = :s')
            ->setParameter('u', $user)
            ->setParameter('s', $server)
            ->getQuery()
            ->getResult();
    }

    public function getExpiredGroups(): array
    {
        return $this->er->createQueryBuilder('usergroup')
            ->where('usergroup.expireAt != 0 AND usergroup.expireAt <= :now')
            ->setParameter('now', time())
            ->getQuery()
            ->getResult();
    }

    public function getPreExpiredGroups(int $days = 3): array
    {
        return $this->er->createQueryBuilder('usergroup')
            ->where('usergroup.expireAt != 0 AND usergroup.expireAt <= :time')
            ->setParameter('time', time() + (86400 * $days))
            ->getQuery()
            ->getResult();
    }

    public function create(UserGroup $userGroup, bool $flush = true): EntityManagerInterface
    {
        $this->em->persist($userGroup);

        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }

    public function update(UserGroup $userGroup, bool $flush = true): EntityManagerInterface
    {
        if ($flush) {
            $this->em->flush($userGroup);
        }

        return $this->em;
    }

    public function delete(UserGroup $userGroup, bool $flush = true): EntityManagerInterface
    {
        $this->em->remove($userGroup);

        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }
}