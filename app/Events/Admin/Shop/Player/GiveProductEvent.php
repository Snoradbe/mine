<?php


namespace App\Events\Admin\Shop\Player;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class GiveProductEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var User
     */
    private $target;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var int
     */
    private $amount;

    /**
     * GiveProductEvent constructor.
     * @param User $admin
     * @param User $target
     * @param Product $product
     * @param Server $server
     * @param int $amount
     */
    public function __construct(User $admin, User $target, Product $product, Server $server, int $amount)
    {
        $this->admin = $admin;
        $this->target = $target;
        $this->product = $product;
        $this->server = $server;
        $this->amount = $amount;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return User
     */
    public function getTarget(): User
    {
        return $this->target;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'target' => [
                'id' => $this->target->getId(),
                'name' => $this->target->getName()
            ],
            'product' => $this->product->toArray(),
            'server' => $this->server->toArray(),
            'amount' => $this->amount
        ];
    }
}