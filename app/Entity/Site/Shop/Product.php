<?php


namespace App\Entity\Site\Shop;


use App\Entity\Site\Server;
use App\Exceptions\Exception;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

/**
 * Shop\Product
 *
 * @ORM\Table(name="pr_shop_products")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Product
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
     * @var Server|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Server")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="server_id", referencedColumnName="id")
     * })
     */
    private $server;

    /**
     * @var Item|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Shop\Item")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * })
     */
    private $item;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Site\Shop\Packet", mappedBy="product", cascade={"persist"})
     */
    private $items;

    /*
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Site\Shop\Pack", mappedBy="product", cascade={"persist"})
     */
    //private $items;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Shop\Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * })
     */
    private $category;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount = 1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    private $data = null;

    /**
     * @var array
     */
    private $arrayData;

    /**
     * @var int|null
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;

    /**
     * @var int|null
     *
     * @ORM\Column(name="price_coins", type="integer", nullable=true)
     */
    private $priceCoins;

    /**
     * @var int
     *
     * @ORM\Column(name="discount", type="integer")
     */
    private $discount = 0;

    /**
     * @var \DateTimeImmutable|null
     *
     * @ORM\Column(name="discount_time", type="datetime_immutable")
     */
    private $discountTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="for_groups", type="string", nullable=true)
     */
    private $for;

    /**
     * @var array
     */
    private $forArray;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = false;

    /**
     * @var int
     *
     * @ORM\Column(name="buys", type="integer")
     */
    private $buys = 0;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable")
     */
    private $createdAt;

    /**
     * Product constructor.
     *
     * @param Server|null $server
     * @param Item|null $item
     * @param Category $category
     * @param int $amount
     * @param array $data
     * @param int $price
     * @param int $priceCoins
     * @param int $discount
     * @param \DateTimeImmutable|null $discountTime
     * @param array $for
     * @param null|string $name
     */
    public function __construct(
        ?Server $server,
        ?Item $item,
        Category $category,
        int $amount,
        array $data,
        int $price,
        int $priceCoins = 0,
        int $discount = 0,
        ?\DateTimeImmutable $discountTime = null,
        array $for = [],
        ?string $name = null)
    {
        //$this->items = new ArrayCollection();

        $this->server = $server;
        $this->item = $item;
        $this->category = $category;
        $this->amount = $amount;
        $this->setData($data);
        $this->price = $price > 0 ? $price : null;
        $this->priceCoins = $priceCoins > 0 ? $priceCoins : null;
        $this->discount = $discount;
        $this->discountTime = $discountTime;
        $this->for = !empty($for) ? implode(',', $for) : null;
        $this->forArray = $for;
        $this->name = $name;
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->server;
    }

    /**
     * @param Server|null $server
     * @return Product
     */
    public function setServer(?Server $server): Product
    {
        $this->server = $server;

        return $this;
    }

    /**
     * @return Item|null
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }

    /**
     * @param Item|null $item
     * @return Product
     */
    public function setItem(?Item $item): Product
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Product
     */
    public function setCategory(Category $category): Product
    {
        $this->category = $category;

        return $this;
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
     * @return Product
     */
    public function setAmount(int $amount): Product
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = empty($data) ? null : json_encode($data);
        $this->arrayData = $data;
    }

    /**
     * @return array
     */
    public function getArrayData(): array
    {
        if (is_null($this->arrayData)) {
            $this->arrayData = is_null($this->data) ? [] : json_decode($this->data, true);
        }

        return $this->arrayData;
    }

    /**
     * @param string $valute
     * @return int
     * @throws Exception
     */
    public function getPriceByValute(string $valute): int
    {
        switch ($valute)
        {
            case 'rub': return $this->price;
            case 'coins': return $this->priceCoins;

            default: throw new Exception('Валюта не найдена!');
        }
    }

    /**
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return Product
     */
    public function setPrice(int $price): Product
    {
        $this->price = $price > 0 ? $price : null;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriceCoins(): ?int
    {
        return $this->priceCoins;
    }

    /**
     * @param int $priceCoins
     * @return Product
     */
    public function setPriceCoins(int $priceCoins): Product
    {
        $this->priceCoins = $priceCoins > 0 ? $priceCoins : null;

        return $this;
    }

    /**
     * @return int
     */
    public function getDiscount(): int
    {
        return $this->discount;
    }

    /**
     * @return int
     */
    public function getRealDiscount(): int
    {
        if ($this->discount > 0 && (is_null($this->discountTime) || $this->discountTime->getTimestamp() > time())) {
            return $this->discount;
        }

        return 0;
    }

    /**
     * @param int $discount
     * @return Product
     */
    public function setDiscount(int $discount): Product
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDiscountTime(): ?\DateTimeImmutable
    {
        return $this->discountTime;
    }

    /**
     * @param \DateTimeImmutable|null $discountTime
     * @return Product
     */
    public function setDiscountTime(?\DateTimeImmutable $discountTime): Product
    {
        $this->discountTime = $discountTime;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFor(): ?string
    {
        return $this->for;
    }

    /**
     * @param array|null $for
     * @return Product
     */
    public function setFor(?array $for = null): Product
    {
        $this->for = !empty($for) ? implode(',', $for) : null;
        $this->forArray = empty($for) ? [] : $for;

        return $this;
    }

    /**
     * @return array
     */
    public function getForArray(): array
    {
        if(is_null($this->forArray)) {
            $this->forArray = is_null($this->for) ? [] : explode(',', $this->for);
        }

        return $this->forArray;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        if(!is_null($this->name)) {
            return $this->name;
        }

        return $this->item->getName();
    }

    /**
     * @param null|string $name
     * @return Product
     */
    public function setName(?string $name): Product
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return Product
     */
    public function setEnabled(bool $enabled): Product
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return int
     */
    public function getBuys(): int
    {
        return $this->buys;
    }

    /**
     * @param int $amount
     */
    public function addBuy(int $amount = 1): void
    {
        $this->buys += $amount;
    }

    /**
     * @param int $amount
     */
    public function removeBuy(int $amount = 1): void
    {
        $this->buys -= $amount;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function generateCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function toArray(bool $secret = false): array
    {
        $data =  [
            'id' => $this->id,
            'server' => is_null($this->server) ? null : $this->server->toArray(),
            'name' => is_null($this->item) ? $this->name : $this->item->getName(),
            'product_name' => $this->name,
            //'name' => !empty($this->name) ? $this->name : $this->item->getName(),
            'item' => is_null($this->item) ? null : $this->item->toArray(),
            'category' => $this->category->toArray(),
            'amount' => $this->amount,
            'data' => $this->getArrayData(),
            'price' => $this->price,
            'price_coins' => $this->priceCoins,
            'discount' => $this->discount,
            'discount_time' => is_null($this->discountTime) ? null : $this->discountTime->getTimestamp(),
            'for' => $this->getForArray(),
            'created_at' => $this->createdAt->getTimestamp(),
            'items' => !is_null($this->item) ? null : array_map(function (Packet $packet) {
                return $packet->toItemArray();
            }, $this->items->toArray()),
        ];

        if ($secret) {
            $data['buys'] = $this->buys;
            $data['enabled'] = $this->enabled;
        }

        return $data;
    }
}