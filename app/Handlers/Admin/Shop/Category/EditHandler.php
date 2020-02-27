<?php


namespace App\Handlers\Admin\Shop\Category;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Category;
use App\Entity\Site\User;
use App\Events\Admin\Shop\Category\EditCategoryEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Category\CategoryRepository;

class EditHandler
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

    public function handle(User $admin, int $categoryId, ?int $parentId, ?int $serverId, string $name, int $weight): void
    {
        $server = is_null($serverId) ? null : $this->getServer($serverId);
        $parent = is_null($parentId) ? null : $this->getCategory($parentId);

        $category = $this->getCategory($categoryId);
        $old = clone $category;
        if (!is_null($parent) && $category->getId() == $parent->getId()) {
            throw new Exception('Категория не может быть родителем самой себе');
        }

        $category->setServer(is_null($parent) ? $server : $parent->getServer());
        $category->setParentCategory($parent);
        $category->setName($name);
        $category->setWeight($weight);

        $this->categoryRepository->update($category);

        event(new EditCategoryEvent($admin, $server, $category, $old));
    }
}