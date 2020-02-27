<?php


namespace App\Events\Admin\Shop\Player;


use App\Entity\Game\Shop\RealMine;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class RemovePurchaseEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var User
     */
    private $target;

    /**
     * @var RealMine
     */
    private $storageProduct;

    /**
     * @var int
     */
    private $price;

    /**
     * @var string
     */
    private $type;

    /**
     * RemovePurchaseEvent constructor.
     * @param User $admin
     * @param User $target
     * @param RealMine $storageProduct
     * @param int $price
     * @param string $type
     */
    public function __construct(User $admin, User $target, RealMine $storageProduct, int $price, string $type)
    {
        $this->admin = $admin;
        $this->target = $target;
        $this->storageProduct = $storageProduct;
        $this->server = $storageProduct->getServer();
        $this->price = $price;
        $this->type = $type;
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
     * @return RealMine
     */
    public function getStorageProduct(): RealMine
    {
        return $this->storageProduct;
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
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
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
            'product' => $this->storageProduct->getProduct()->toArray(),
            'amount' => $this->storageProduct->getAmount(),
            'valute' => !is_null($this->storageProduct->getStatistic())
                ? $this->storageProduct->getStatistic()->getValute()
                : null,
            'sum' => $this->price,
            'buy_date' => $this->storageProduct->getDate()->getTimestamp(),
            'type' => $this->type
        ];
    }
}