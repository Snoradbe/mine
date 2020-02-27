<?php


namespace App\Events\Client\Shop;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Client\EventWithServer;
use App\Events\Event;

class CancelPurchaseEvent extends ClientEvent implements Event
{
    use EventWithServer;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var string
     */
    private $valute;

    /**
     * @var int
     */
    private $sum;

    /**
     * @var int
     */
    private $fee;

    /**
     * CancelPurchaseEvent constructor.
     * @param User $user
     * @param Server $server
     * @param Product $product
     * @param string $valute
     * @param int $sum
     * @param int $fee
     */
    public function __construct(User $user, Server $server, Product $product, string $valute, int $sum, int $fee)
    {
        parent::__construct($user);

        $this->server = $server;
        $this->product = $product;
        $this->valute = $valute;
        $this->sum = $sum;
        $this->fee = $fee;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return string
     */
    public function getValute(): string
    {
        return $this->valute;
    }

    /**
     * @return int
     */
    public function getSum(): int
    {
        return $this->sum;
    }

    /**
     * @return int
     */
    public function getFee(): int
    {
        return $this->fee;
    }

    /**
     * @return int
     */
    public function getResultSum(): int
    {
        return $this->sum - (ceil( $this->sum * ($this->fee / 100)));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'product' => [
                'id' => $this->product->getId(),
                'name' => $this->product->getProductName(),
                'item' => is_null($this->product->getItem()) ? null : $this->product->getItem()->toArray(),
                'price' => $this->product->getPriceByValute($this->valute)
            ],
            'valute' => $this->valute,
            'price' => $this->sum,
            'fee' => $this->fee,
            'result_sum' => $this->getResultSum()
        ];
    }
}