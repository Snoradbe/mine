<?php


namespace App\Events\Admin\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class DeleteProductEvent implements Event
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
     * @var Product
     */
    private $product;

    /**
     * AddProductEvent constructor.
     * @param User $admin
     * @param Server|null $server
     * @param Product $product
     */
    public function __construct(User $admin, ?Server $server, Product $product)
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->product = $product;
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
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'product' => $this->product->toArray()
        ];
    }
}