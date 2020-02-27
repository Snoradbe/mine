<?php


namespace App\Repository\Site\Shop\ItemType;


use App\Entity\Site\Shop\ItemType;
use App\Repository\DoctrineConstructor;

class DoctrineItemTypeRepository implements ItemTypeRepository
{
    use DoctrineConstructor;

    public function find(string $id): ?ItemType
    {
        return $this->er->find($id);
    }

    public function getAll(): array
    {
        return $this->er->findAll();
    }
}