<?php


namespace App\Events\Client\Shop;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Client\EventWithServer;
use App\Events\Event;

class BuyProductEvent extends ClientEvent implements Event
{
    use EventWithServer;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $valute;

    /**
     * Цена, которую оплатил игрок. Со скидками и тд.
     * @var int
     */
    private $price;

    /**
     * @var int
     */
    private $discount;

    /**
     * BuyProductEvent constructor.
     * @param User $user
     * @param Server $server
     * @param Product $product
     * @param int $amount
     * @param string $valute
     * @param int $price
     * @param int $discount
     */
    public function __construct(User $user, Server $server, Product $product, int $amount, string $valute, int $price, int $discount)
    {
        parent::__construct($user);

        $this->server = $server;
        $this->product = $product;
        $this->amount = $amount;
        $this->valute = $valute;
        $this->price = $price;
        $this->discount = $discount;
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
    public function getAmount(): int
    {
        return $this->amount;
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
    public function getValute(): string
    {
        return $this->valute;
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
                'real_price' => $this->valute == 'rub' ? $this->product->getPrice() : $this->product->getPriceCoins(),
                'discount' => $this->discount
            ],
            'amount' => $this->amount,
            'valute' => $this->valute,
            'price' => $this->price
        ];
    }
}