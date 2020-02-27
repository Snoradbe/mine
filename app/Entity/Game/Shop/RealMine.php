<?php


namespace App\Entity\Game\Shop;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\Shop\Statistic;
use App\Entity\Site\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Shop\RealMine
 *
 * @ORM\Table(name="pr_shop_storage", indexes={@ORM\Index(name="pr_shop_storage_item_id_foreign", columns={"item_id"}), @ORM\Index(name="pr_shop_storage_server_id_foreign", columns={"server_id"}), @ORM\Index(name="pr_shop_storage_user_id_foreign", columns={"user_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class RealMine
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $amount;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data", type="text", length=65535, nullable=true, options={"comment"="Например, если игрок после покупки захочет зачарить предмет"})
     */
    private $data;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="date", type="datetime_immutable", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $date;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Shop\Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product;

    /**
     * @var Server
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Server")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="server_id", referencedColumnName="id")
     * })
     */
    private $server;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    private $user;

    /**
     * @var Statistic|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Shop\Statistic")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="statistic_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $statistic;

    /**
     * @var array
     */
    private $arrayData;

    /**
     * RealMine constructor.
     *
     * @param User $user
     * @param Server $server
     * @param Product $product
     * @param Statistic|null $statistic
     * @param int $amount
     * @param array $data
     */
    public function __construct(User $user, Server $server, Product $product, ?Statistic $statistic, int $amount, array $data)
    {
        $this->user = $user;
        $this->server = $server;
        $this->product = $product;
        $this->statistic = $statistic;
        $this->amount = $amount;
        $this->setData($data);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
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
     * @return null|string
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getArrayData(): array
    {
        if (is_null($this->arrayData)) {
            $this->arrayData = is_null($this->data) ? [] : (array) json_decode($this->data, true);
        }

        return $this->arrayData;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->arrayData = $data;
        $this->data = empty($data) ? null : json_encode($data);
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return Statistic|null
     */
    public function getStatistic(): ?Statistic
    {
        return $this->statistic;
    }

    /**
     * @return bool
     */
    public function isCancelable(): bool
    {
        return
            !is_null($this->statistic) &&
            $this->date->getTimestamp() + config('site.shop.cancel_time', 3600) > time();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->date = new \DateTimeImmutable();
    }

    public function toArray(bool $secret = false): array
    {
        return [
            'id' => $this->id,
            'product' => $this->product->toArray($secret),
            'server' => $this->server->toArray(),
            'amount' => $this->amount,
            'price' => is_null($this->statistic) ? null : $this->statistic->getPrice(),
            'valute' => is_null($this->statistic) ? null : $this->statistic->getValute(),
            'data' => $this->getArrayData(),
            'date' => $this->date->getTimestamp(),
            'cancelable' => $this->isCancelable(),
            'user' => $secret ? $this->user->toArray($secret) : null
        ];
    }
}