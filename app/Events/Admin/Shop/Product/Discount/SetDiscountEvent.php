<?php


namespace App\Events\Admin\Shop\Product\Discount;


use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class SetDiscountEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var int
     */
    private $discount;

    /**
     * @var \DateTimeImmutable|null
     */
    private $date;

    /**
     * SetDiscountEvent constructor.
     * @param User $admin
     * @param Product $product
     * @param int $discount
     * @param \DateTimeImmutable|null $date
     */
    public function __construct(User $admin, Product $product, int $discount, ?\DateTimeImmutable $date)
    {
        $this->admin = $admin;
        $this->product = $product;
        $this->discount = $discount;
        $this->date = $date;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getDiscount(): int
    {
        return $this->discount;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'product' => $this->product->toArray(),
            'discount' => $this->discount,
            'date' => is_null($this->date) ? null : $this->date->getTimestamp()
        ];
    }
}