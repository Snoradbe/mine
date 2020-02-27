<?php


namespace App\Repository\Site\Shop\ItemType;


use App\Entity\Site\Shop\ItemType;

interface ItemTypeRepository
{
    public function find(string $id): ?ItemType;

    public function getAll(): array;
}