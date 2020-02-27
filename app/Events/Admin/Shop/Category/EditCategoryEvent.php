<?php


namespace App\Events\Admin\Shop\Category;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Category;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class EditCategoryEvent implements Event
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
     * @var Category
     */
    private $old;

    /**
     * EditCategoryEvent constructor.
     * @param User $admin
     * @param Server|null $server
     * @param Category $category
     * @param Category $old
     */
    public function __construct(User $admin, ?Server $server, Category $category, Category $old)
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->category = $category;
        $this->old = $old;
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
     * @return Category
     */
    public function getOld(): Category
    {
        return $this->old;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'category' => $this->category->toArray(),
            'old' => $this->old->toArray()
        ];
    }
}