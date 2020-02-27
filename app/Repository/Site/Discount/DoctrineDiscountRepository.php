<?php


namespace App\Repository\Site\Discount;


use App\Entity\Site\Discount;
use App\Repository\DoctrineClearCache;
use App\Repository\DoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineDiscountRepository implements DiscountRepository
{
    use DoctrineConstructor, DoctrineClearCache;

    public function getAll(bool $expired = false): array
    {
        $query = $this->er->createQueryBuilder('disc')
            ->orderBy('disc.id', 'DESC');

        if (!$expired) {
            $query->where('disc.timeEnd > :now')
                ->setParameter('now', time());
        }

        return $query->getQuery()
            ->useResultCache(true, 3600)
            ->getResult();
    }

    public function find(int $id): ?Discount
    {
        return $this->er->find($id);
    }

    public function create(Discount $discount): void
    {
        $this->clearResultCache();

        $this->em->persist($discount);
        $this->em->flush();
    }

    public function delete(Discount $discount): void
    {
        $this->clearResultCache();

        $this->em->remove($discount);
        $this->em->flush();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
}