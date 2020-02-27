<?php


namespace App\Repository\Site\Discount;


use App\Entity\Site\Discount;

interface DiscountRepository
{
    /**
     * @param bool $expired
     * @return Discount[]
     */
    public function getAll(bool $expired = false): array;

    /**
     * @param int $id
     * @return Discount|null
     */
    public function find(int $id): ?Discount;

    /**
     * @param Discount $discount
     */
    public function create(Discount $discount): void;

    /**
     * @param Discount $discount
     */
    public function delete(Discount $discount): void;
}