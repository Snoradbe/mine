<?php


namespace App\Repository\Site\Shop\Item;


use App\Entity\Site\Shop\Item;
use App\Repository\DoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineItemRepository implements ItemRepository
{
    use DoctrineConstructor;

    public function find(int $id): ?Item
    {
        return $this->er->find($id);
    }

    public function getAll(): array
    {
        return $this->er->findAll();
    }

    public function create(Item $item, bool $flush = true): EntityManagerInterface
    {
        $this->em->persist($item);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }

    public function update(Item $item, bool $flush = true): EntityManagerInterface
    {
        if ($flush) {
            $this->em->flush($item);
        }

        return $this->em;
    }

    public function delete(Item $item, bool $flush = true): EntityManagerInterface
    {
        $this->em->remove($item);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }
}