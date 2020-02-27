<?php


namespace App\Events\Admin\Shop\Category;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Category;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class AddCategoryEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Server|null
     */
    private $server;

    /**
     * @var Category
     */
    private $category;

    /**
     * AddCategoryEvent constructor.
     * @param User $admin
     * @param Server|null $server
     * @param Category $category
     */
    public function __construct(User $admin, ?Server $server, Category $category)
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->category = $category;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->server;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'category' => $this->category->toArray()
        ];
    }
}