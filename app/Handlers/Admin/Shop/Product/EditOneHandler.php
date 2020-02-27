<?php


namespace App\Handlers\Admin\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Category;
use App\Entity\Site\Shop\Item;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Admin\Shop\Product\EditProductEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Category\CategoryRepository;
use App\Repository\Site\Shop\Item\ItemRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Services\Shop\Enchants\Enchanting;

class EditOneHandler
{
    private $productRepository;

    private $serverRepository;

    public function __construct(
        ProductRepository $productRepository,
        ServerRepository $serverRepository,
        CategoryRepository $categoryRepository,
        ItemRepository $itemRepository)
    {
        $this->productRepository = $productRepository;
        $this->serverRepository = $serverRepository;
        $this->categoryRepository = $categoryRepository;
        $this->itemRepository = $itemRepository;
    }

    private function getProduct(int $id): Product
    {
        $product = $this->productRepository->find($id);
        if (is_null($product)) {
            throw new Exception('Товар не найден!');
        }

        return $product;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    private function getCategory(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    private function getItem(int $id): Item
    {
        $item = $this->itemRepository->find($id);
        if (is_null($item)) {
            throw new Exception('Итем не найден!');
        }

        return $item;
    }

    public function enable(int $productId): bool
    {
        $product = $this->getProduct($productId);

        $product->setEnabled(!$product->isEnabled());

        $this->productRepository->update($product);

        return $product->isEnabled();
    }

    public function handle(
        User $admin,
        int $productId,
        ?int $serverId,
        int $categoryId,
        ?int $childCategoryId,
        int $itemId,
        int $amount,
        int $price,
        int $priceCoins,
        int $discount,
        ?string $discountDate,
        array $enchants): Product
    {
        $product = $this->getProduct($productId);
        $old = clone $product;

        if ($discount > 0) {
            if (empty($discountDate)) {
                throw new Exception('Введите дату!');
            }
            $discountDate = strtotime($discountDate);
            if (time() >= $discountDate) {
                throw new Exception('Дата скидки должна быть больше чем сейчас!');
            }
            try {
                $discountDate = new \DateTimeImmutable(date('Y-m-d', $discountDate));
            } catch (\Exception $exception) {
                $discountDate = null;
                $discount = 0;
                throw new Exception($exception->getMessage());
            }
        } else {
            $discountDate = null;
        }

        $server = is_null($serverId) ? null : $this->getServer($serverId);
        $category = $this->getCategory($categoryId);
        if (is_null($category)) {
            throw new Exception('Категория не найдена!');
        }
        if (is_null($server) && !is_null($category->getServer())) {
            throw new Exception('Категория привязаная к другому серверу не может быть выбрана для этого сервера!');
        } elseif (!is_null($server) && (!is_null($category->getServer()) && $category->getServer()->getId() != $server->getId())) {
            throw new Exception('Категория привязаная к другому серверу не может быть выбрана для этого сервера!');
        }

        $childCategory = null;
        if (!is_null($childCategoryId)) {
            $childCategory = $this->getCategory($childCategoryId);
            if (is_null($category)) {
                throw new Exception('Дочерняя категория не найдена!');
            }
            if (is_null($childCategory->getParentCategory()) || $childCategory->getParentCategory()->getId() != $category->getId()) {
                throw new Exception('Дочерняя категория не принадлежит выбранной родительской!');
            }
        }

        $item = $this->getItem($itemId);

        $product->setServer($server);
        $product->setItem($item);
        $product->setCategory(is_null($childCategory) ? $category : $childCategory);
        $product->setAmount($amount);
        $product->setPrice($price);
        $product->setPriceCoins($priceCoins);
        $product->setDiscount($discount);
        $product->setDiscountTime($discountDate);

        Enchanting::enchantProduct($product, $enchants);

        $this->productRepository->update($product);

       event(new EditProductEvent($admin, $server, $product, $old));

        return $product;
    }
}