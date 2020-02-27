<?php


namespace App\Handlers\Admin\Shop\Product;


use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Admin\Shop\Product\Discount\SetDiscountEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Shop\Product\ProductRepository;

class DiscountHandler
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    private function getProduct(int $id): Product
    {
        $product = $this->productRepository->find($id);
        if (is_null($product)) {
            throw new Exception('Товар не найден!');
        }

        return $product;
    }

    public function handle(User $admin, int $productId, int $discount, string $date): array
    {
        $product = $this->getProduct($productId);

        $date = strtotime($date);

        if ($discount > 0 && $date < time()) {
            throw new Exception('Дата должна быть больше сегодняшнего числа!');
        }

        $product->setDiscount($discount);
        try {
            $date = $discount == 0 ? null : new \DateTimeImmutable(date('Y-m-d', $date));
            $product->setDiscountTime($date);
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        $this->productRepository->update($product);

        event(new SetDiscountEvent($admin, $product, $discount, $date));

        return [$product->getDiscount(), is_null($product->getDiscountTime()) ? 0 : $product->getDiscountTime()->getTimestamp()];
    }
}