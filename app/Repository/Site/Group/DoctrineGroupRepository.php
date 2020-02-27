<?php


namespace App\Repository\Site\Group;


use App\Entity\Site\Group;
use App\Repository\DoctrineConstructor;

class DoctrineGroupRepository implements GroupRepository
{
    use DoctrineConstructor;

    public function find(int $id): ?Group
    {
        return $this->er->find($id);
    }

    public function findByName(string $name): ?Group
    {
        return $this->er->findOneBy(['name' => $name]);
    }

    public function getAll(string $order = 'weight', string $sort = 'DESC'): array
    {
        return $this->er->createQueryBuilder('gr')
            ->orderBy('gr.' . $order, $sort)
            ->getQuery()
            ->getResult();
    }

    public function getAllDonate(bool $onlyPrimary = false, string $sort = 'DESC'): array
    {
        $query = $this->er->createQueryBuilder('gr');

        if ($onlyPrimary) {
            $query->where('gr.isAdmin != 1 AND gr.isPrimary = 1');
        } else {
            $query->where('gr.isAdmin != 1');
        }

        $query->orderBy('gr.weight', $sort);

        return $query->getQuery()->getResult();
    }

    public function getAllAdmin(bool $onlyPrimary = false, string $sort = 'DESC'): array
    {
        $query = $this->er->createQueryBuilder('gr');

        if ($onlyPrimary) {
            $query->where('gr.isAdmin = 1 AND gr.isPrimary = 1');
        } else {
            $query->where('gr.isAdmin = 1');
        }

        $query->orderBy('gr.weight', $sort);

        return $query->getQuery()->getResult();
    }

    public function create(Group $group): void
    {
        $this->em->persist($group);
        $this->em->flush();
    }

    public function update(Group $group): void
    {
        $this->em->flush($group);
    }

    public function delete(Group $group): void
    {
        $this->em->remove($group);
        $this->em->flush();
    }
}