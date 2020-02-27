<?php


namespace App\Events\Admin\Shop\Item;


use App\Entity\Site\Shop\Item;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class AddItemEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Item
     */
    private $item;

    /**
     * AddItemEvent constructor.
     * @param User $admin
     * @param Item $item
     */
    public function __construct(User $admin, Item $item)
    {
        $this->admin = $admin;
        $this->item = $item;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Item
     */
    public function getCategory(): Item
    {
        return $this->item;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'item' => $this->item->toArray()
        ];
    }
}