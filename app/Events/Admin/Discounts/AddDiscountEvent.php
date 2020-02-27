<?php


namespace App\Events\Admin\Discounts;


use App\Entity\Site\Discount;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class AddDiscountEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Discount
     */
    private $discount;

    /**
     * AddDiscountEvent constructor.
     * @param User $admin
     * @param Discount $discount
     */
    public function __construct(User $admin, Discount $discount)
    {
        $this->admin = $admin;
        $this->discount = $discount;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Discount
     */
    public function getDiscount(): Discount
    {
        return $this->discount;
    }

    /**
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->discount->getServer();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'discount' => $this->discount->toArray()
        ];
    }
}