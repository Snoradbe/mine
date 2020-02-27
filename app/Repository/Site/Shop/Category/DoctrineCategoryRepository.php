<?php


namespace App\Repository\Site\Shop\Category;


use App\Entity\Site\Shop\Category;
use App\Repository\DoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCategoryRepository implements CategoryRepository
{
    use DoctrineConstructor;

    public function find(int $id): ?Category
    {
        return $this->er->find($id);
    }

    public function getAll(): array
    {
        return $this->er->findAll();
    }

    public function getAllParents(): array
    {
        return $this->er->createQueryBuilder('cat')
            ->where('cat.parentCategory IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function getAllChildes(): array
    {
        return $this->er->createQueryBuilder('cat')
            ->where('cat.parentCategory IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function create(Category $category, bool $flush = true): EntityManagerInterface
    {
        $this->em->persist($category);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }

    public function update(Category $category, bool $flush = true): EntityManagerInterface
    {
        if ($flush) {
            $this->em->flush($category);
        }

        return $this->em;
    }

    public function delete(Category $category, bool $flush = true): EntityManagerInterface
    {
        $this->em->remove($category);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }
}