<?php


namespace App\Services\Shop\Distributor\RealMine;


use App\Entity\Game\Shop\RealMine;
use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\Shop\Statistic;
use App\Entity\Site\User;
use App\Repository\Game\Shop\RealMine\RealMineRepository;
use App\Services\Shop\Distributor\Distributor;

class RealMineDistributor implements Distributor
{
    private $shopStorageRepository;

    private $enchanting;

    public function __construct(RealMineRepository $repository, Enchanting $enchanting)
    {
        $this->shopStorageRepository = $repository;
        $this->enchanting = $enchanting;
    }

    public function distribute(User $user, Server $server, Product $product, ?Statistic $statistic, int $amount, array $data): void
    {
        $entity = new RealMine(
            $user,
            $server,
            $product,
            $statistic,
            $amount,
            $data
        );

        //$this->enchanting->enchant($item, $entity);

        $this->shopStorageRepository->create($entity);
    }
}