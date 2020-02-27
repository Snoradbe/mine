<?php


namespace App\Services\Shop\Distributor;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\Shop\Statistic;
use App\Entity\Site\User;

interface Distributor
{
    /**
     * @param User $user
     * @param Server $server
     * @param Product $product
     * @param Statistic|null $statistic
     * @param int $amount
     * @param array $data
     */
    public function distribute(User $user, Server $server, Product $product, ?Statistic $statistic, int $amount, array $data): void;
}