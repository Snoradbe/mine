<?php


namespace App\Events\Admin\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Packet;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class AddProductEvent implements Event
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
     * @var Packet[]
     */
    private $packets;

    /**
     * AddProductEvent constructor.
     * @param User $admin
     * @param Server|null $server
     * @param Product $product
     * @param Packet[] $packets
     */
    public function __construct(User $admin, ?Server $server, Product $product, array $packets = [])
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->product = $product;
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
            'product' => $this->product->toArray()
        ];
    }
}