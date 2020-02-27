<?php


namespace App\Repository\Site\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Services\Shop\Search;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepository
{
    public function setPerPage(?int $perPage): ProductRepository;

    public function find(int $id): ?Product;

    public function getAll(Search $search, bool $onlyEnabled, int $page): LengthAwarePaginator;

    public function getAllWithDiscount(): array;

    public function getTopBuysProducts(int $limit = 10): array;

    public function randomDiscounts(?Server $server, int $min, int $max, int $days, string $order): void;

    public function removeExpiredDiscounts(): void;

    public function create(Product $product, bool $flush = true): EntityManagerInterface;

    public function update(Product $product, bool $flush = true): EntityManagerInterface;

    public function delete(Product $product, bool $flush = true): EntityManagerInterface;
}