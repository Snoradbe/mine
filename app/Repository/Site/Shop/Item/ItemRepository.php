<?php


namespace App\Repository\Site\Shop\Item;


use App\Entity\Site\Shop\Item;
use Doctrine\ORM\EntityManagerInterface;

interface ItemRepository
{
    public function find(int $id): ?Item;

    public function getAll(): array;

    public function create(Item $item, bool $flush = true): EntityManagerInterface;

    public function update(Item $item, bool $flush = true): EntityManagerInterface;

    public function delete(Item $item, bool $flush = true): EntityManagerInterface;
}