<?php


namespace App\Entity\Site\Shop;


use Doctrine\ORM\Mapping as ORM;

/**
 * Packet
 *
 * @ORM\Table(name="pr_shop_packs", indexes={@ORM\Index(name="pr_shop_packs_item_id_foreign", columns={"item_id"}), @ORM\Index(name="pr_shop_packs_product_id_foreign", columns={"product_id"})})
 * @ORM\Entity
 */
class Packet
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
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
     * @ORM\Column(name="data", type="text", nullable=true, options={"unsigned"=true})
     */
    private $data;

    /**
     * @var Item
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Shop\Item")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * })
     */
    private $item;

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
     * @var array
     */
    private $dataArray;

    /**
     * Packet constructor.
     *
     * @param Product $product
     * @param Item $item
     * @param int $amount
     * @param array $data
     */
    public function __construct(Product $product, Item $item, int $amount, array $data = [])
    {
        $this->product = $product;
        $this->item = $item;
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
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
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
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getDataArray(): array
    {
        if (is_null($this->dataArray)) {
            $this->dataArray = empty($this->data) ? [] : (array) json_decode($this->data);
        }

        return $this->dataArray;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = empty($data) ? null : json_encode($data);
        $this->dataArray = $data;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'product' => $this->product->toArray(),
            'item' => $this->item->toArray(),
            'data' => $this->getDataArray()
        ];
    }

    /**
     * @return array
     */
    public function toItemArray(): array
    {
        $data = $this->item->toArray();
        $data['amount'] = $this->amount;

        $data['data'] = $this->getDataArray();

        return $data;
    }
}