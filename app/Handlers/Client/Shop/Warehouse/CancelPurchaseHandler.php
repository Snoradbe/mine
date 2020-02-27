<?php


namespace App\Handlers\Client\Shop\Warehouse;


use App\Entity\Game\Shop\RealMine;
use App\Entity\Site\User;
use App\Events\Client\Shop\CancelPurchaseEvent;
use App\Exceptions\Exception;
use App\Repository\Game\Shop\RealMine\RealMineRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Repository\Site\Shop\Statistic\StatisticRepository;
use App\Repository\Site\User\UserRepository;
use App\Services\Discounts\Discounts;

class CancelPurchaseHandler
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
            throw new Exception('Товар не найден на вашем складе!');
        }

        return $product;
    }

    public function handle(User $user, int $storageId): array
    {
        $storageProduct = $this->getStorageProduct($storageId);
        if ($storageProduct->getUser()->getId() !== $user->getId()) {
            throw new Exception('Товар не найден на вашем складе!');
        }

        if (!$storageProduct->isCancelable()) {
            throw new Exception('К сожалению, отменить покупку этого товара невозможно. Возможно истекло время возврата.');
        }

        if (is_null($storageProduct->getStatistic()) || $storageProduct->getStatistic()->getPrice() < 1) {
            throw new Exception('Вы получили этот товар бесплатно, его невозможно вернуть назад!');
        }

        $price = $storageProduct->getStatistic()->getPrice();
        $fee = config('site.shop.cancel_fee', 0);
        if ($fee > 0) {
            $price = Discounts::getPriceWithDiscount($price, $fee);
        }

        if ($storageProduct->getStatistic()->getValute() == 'coins') {
            $user->depositCoins($price);
        } else {
            $user->depositMoney($price);
        }

        $this->userRepository->update($user);

        $storageProduct->getProduct()->removeBuy();

        $this->productRepository->update($storageProduct->getProduct());

        $this->storageRepository->delete($storageProduct);

        $this->statisticsRepository->delete($storageProduct->getStatistic());

        event(new CancelPurchaseEvent(
            $user,
            $storageProduct->getServer(),
            $storageProduct->getProduct(),
            $storageProduct->getStatistic()->getValute(),
            $storageProduct->getStatistic()->getPrice(),
            $fee
        ));

        return [$price, $storageProduct->getStatistic()->getValute()];
    }
}