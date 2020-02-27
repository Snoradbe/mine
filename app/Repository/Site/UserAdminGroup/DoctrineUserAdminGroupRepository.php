<?php


namespace App\Repository\Site\UserAdminGroup;


use App\Entity\Site\UserAdminGroup;
use App\Repository\DoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserAdminGroupRepository implements UserAdminGroupRepository
{
    use DoctrineConstructor;

    public function getAll(): array
    {
        return $this->er->findAll();
    }

    public function getAllOnServer(?array $servers): array
    {
        $query = $this->er->createQueryBuilder('uag');
        if (is_null($servers)) {
            $query->where('uag.server IS NULL');
        } else {
            $query->where('uag.server IN (:servers)')
                ->setParameter('servers', $servers);
        }

        return $query->getQuery()->getResult();
    }

    public function find(int $id): ?UserAdminGroup
    {
        return $this->er->find($id);
    }

    public function create(UserAdminGroup $group, bool $flush = true): EntityManagerInterface
    {
        $this->em->persist($group);

        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }

    public function delete(UserAdminGroup $group, bool $flush = true): EntityManagerInterface
    {
        $this->em->remove($group);

        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }
}