<?php


namespace App\Handlers\Admin\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Category;
use App\Entity\Site\Shop\Item;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Admin\Shop\Product\AddProductEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Category\CategoryRepository;
use App\Repository\Site\Shop\Item\ItemRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Services\Shop\Enchants\Enchanting;

class AddOneHandler
{
    private $serverRepository;

    private $categoryRepository;

    private $itemRepository;

    private $productRepository;

    public function __construct(
        ServerRepository $serverRepository,
        CategoryRepository $categoryRepository,
        ItemRepository $itemRepository,
        ProductRepository $productRepository)
    {
        $this->serverRepository = $serverRepository;
        $this->categoryRepository = $categoryRepository;
        $this->itemRepository = $itemRepository;
        $this->productRepository = $productRepository;
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

    public function handle(
        User $admin,
        ?int $serverId,
        int $categoryId,
        ?int $childCategoryId,
        int $itemId,
        int $amount,
        int $price,
        int $priceCoins,
        array $enchants): Product
    {
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

        $product = new Product(
            $server,
            $item,
            is_null($childCategory) ? $category : $childCategory,
            $amount,
            [],
            $price,
            $priceCoins,
            0,
            null,
            [],
            null
        );

        $product->setEnabled(false);

        Enchanting::enchantProduct($product, $enchants);

        $this->productRepository->create($product);

        event(new AddProductEvent($admin, $server, $product));

        return $product;
    }
}