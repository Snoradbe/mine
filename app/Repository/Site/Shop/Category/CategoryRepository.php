<?php


namespace App\Repository\Site\Shop\Category;


use App\Entity\Site\Shop\Category;
use Doctrine\ORM\EntityManagerInterface;

interface CategoryRepository
{
    public function find(int $id): ?Category;

    public function getAll(): array;

    public function getAllParents(): array;

    public function getAllChildes(): array;

    public function create(Category $category, bool $flush = true): EntityManagerInterface;

    public function update(Category $category, bool $flush = true): EntityManagerInterface;

    public function delete(Category $category, bool $flush = true): EntityManagerInterface;
}