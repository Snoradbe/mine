<?php


namespace App\Events\Admin\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Packet;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class EditProductEvent implements Event
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
     * @var Product
     */
    private $old;

    /**
     * @var Packet[]
     */
    private $packets;

    /**
     * EditProductEvent constructor.
     * @param User $admin
     * @param Server|null $server
     * @param Product $product
     * @param Product $old
     * @param Packet[] $packets
     */
    public function __construct(User $admin, ?Server $server, Product $product, Product $old, array $packets = [])
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->product = $product;
        $this->old = $old;
        $this->packets = $packets;
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
     * @return Product
     */
    public function getOld(): Product
    {
        return $this->old;
    }

    /**
     * @return Packet[]
     */
    public function getPackets(): array
    {
        return $this->packets;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'product' => $this->product->toArray(),
            'old' => $this->old->toArray()
        ];
    }
}