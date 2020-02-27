<?php


namespace App\Events\Admin\Shop\Item;


use App\Entity\Site\Shop\Item;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class EditItemEvent implements Event
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
     * @var Item
     */
    private $old;

    /**
     * EditItemEvent constructor.
     * @param User $admin
     * @param Item $item
     * @param Item $old
     */
    public function __construct(User $admin, Item $item, Item $old)
    {
        $this->admin = $admin;
        $this->item = $item;
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
     * @return Item
     */
    public function getCategory(): Item
    {
        return $this->item;
    }

    /**
     * @return Item
     */
    public function getOld(): Item
    {
        return $this->old;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'item' => $this->item->toArray(),
            'old' => $this->old->toArray()
        ];
    }
}