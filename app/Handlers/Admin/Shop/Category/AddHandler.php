<?php


namespace App\Handlers\Admin\Shop\Category;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Category;
use App\Entity\Site\User;
use App\Events\Admin\Shop\Category\AddCategoryEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Category\CategoryRepository;

class AddHandler
{
    private $serverRepository;

    private $categoryRepository;

    public function __construct(ServerRepository $serverRepository, CategoryRepository $categoryRepository)
    {
        $this->serverRepository = $serverRepository;
        $this->categoryRepository = $categoryRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    private function getCategory(int $id): Category
    {
        $category = $this->categoryRepository->find($id);
        if (is_null($category)) {
            throw new Exception('Категория не найдена!');
        }

        return $category;
    }

    public function handle(User $admin, ?int $serverId, ?int $parentId, string $name, int $weight): void
    {
        $server = is_null($serverId) ? null : $this->getServer($serverId);
        $parent = is_null($parentId) ? null : $this->getCategory($parentId);

        $category = new Category(is_null($parent) ? $server : $parent->getServer(), $parent, $name, $weight);

        $this->categoryRepository->create($category);

        event(new AddCategoryEvent($admin, $server, $category));
    }
}