<?php


namespace App\Handlers\Admin\Shop\Player\Warehouse;


use App\Entity\Game\Shop\RealMine;
use App\Entity\Site\User;
use App\Events\Admin\Shop\Player\RemovePurchaseEvent;
use App\Exceptions\Exception;
use App\Repository\Game\Shop\RealMine\RealMineRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Repository\Site\Shop\Statistic\StatisticRepository;
use App\Repository\Site\User\UserRepository;
use App\Services\Settings\DataType;

class RemovePurchaseHandler
{
    private $storageRepository;

    private $userRepository;

    private $statisticsRepository;

    private $productRepository;

    public function __construct(
        RealMineRepository $storageRepository,
        UserRepository $userRepository,
        StatisticRepository $statisticRepository,
        ProductRepository $productRepository)
    {
        $this->storageRepository = $storageRepository;
        $this->userRepository = $userRepository;
        $this->statisticsRepository = $statisticRepository;
        $this->productRepository = $productRepository;
    }

    private function getStorageProduct(int $id): RealMine
    {
        $product = $this->storageRepository->find($id);
        if (is_null($product)) {
            throw new Exception('Товар не найден на складе!');
        }

        return $product;
    }

    private function cashBackStandard(int $price): int
    {
        $fee = settings('shop', DataType::JSON, [])['cancel_fee'] ?? 0;

        return $price - ceil($price * ($fee / 100));
    }

    private function cashBackHalf(int $price): int
    {
        return ceil($price / 2);
    }

    public function handle(User $admin, int $storageId, string $type): void
    {
        $storageProduct = $this->getStorageProduct($storageId);
        $user = $storageProduct->getUser();

        $hasStatistic = !is_null($storageProduct->getStatistic());

        if ($hasStatistic) {
            $price = $storageProduct->getStatistic()->getPrice();
            switch ($type)
            {
                case 'standard': $price = $this->cashBackStandard($price); break;
                case 'half': $price = $this->cashBackHalf($price); break;
                case 'without': $price = 0; break; //без возврата средств
            }

            if ($price > 0) {
                if ($storageProduct->getStatistic()->getValute() == 'coins') {
                    $user->depositCoins($price);
                } else {
                    $user->depositMoney($price);
                }

                $this->userRepository->update($user);
            }
        }

        if ($hasStatistic) {
            $storageProduct->getProduct()->removeBuy();

            $this->productRepository->update($storageProduct->getProduct());
        }

        $this->storageRepository->delete($storageProduct);

        if ($hasStatistic) {
            $this->statisticsRepository->delete($storageProduct->getStatistic());
        }

        event(new RemovePurchaseEvent($admin, $user, $storageProduct, $price ?? 0, $type));
    }
}