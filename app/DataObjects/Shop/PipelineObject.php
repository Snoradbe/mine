<?php


namespace App\DataObjects\Shop;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\Shop\Statistic;
use App\Entity\Site\User;

class PipelineObject
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var Statistic|null
     */
    private $statistic;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var int
     */
    private $resultSum;

    /**
     * @var string
     */
    private $valute;

    /**
     * @var int
     */
    private $discount = 0;

    /**
     * PipelineObject constructor.
     *
     * @param User $user
     * @param Server $server
     * @param Product $product
     * @param int $amount
     * @param string $valute
     */
    public function __construct(User $user, Server $server, Product $product, int $amount, string $valute)
    {
        $this->user = $user;
        $this->server = $server;
        $this->product = $product;
        $this->amount = $amount;
        $this->valute = $valute;
        if($valute == 'coins') {
            $this->resultSum = (int) $product->getPriceCoins() * $amount;
        } else {
            $this->resultSum = (int) $product->getPrice() * $amount;
        }
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
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
     * @return Statistic|null
     */
    public function getStatistic(): ?Statistic
    {
        return $this->statistic;
    }

    /**
     * @param Statistic|null $statistic
     */
    public function setStatistic(?Statistic $statistic): void
    {
        $this->statistic = $statistic;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getResultSum(): int
    {
        return $this->resultSum;
    }

    /**
     * @param int $resultSum
     */
    public function setResultSum(int $resultSum): void
    {
        $this->resultSum = $resultSum;
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
    public function getDiscount(): int
    {
        return $this->discount;
    }

    /**
     * @param int $discount
     */
    public function setDiscount(int $discount): void
    {
        $this->discount = $discount;
    }
}