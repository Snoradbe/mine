<?php


namespace App\Entity\Site\Shop;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shop\Item
 *
 * @ORM\Table(name="pr_shop_items")
 * @ORM\Entity
 */
class Item
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
     * @var ItemType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Shop\ItemType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type", referencedColumnName="id")
     * })
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="data_id", type="string", length=255)
     */
    private $dataId;

    /**
     * Item constructor.
     *
     * @param ItemType $type
     * @param string $name
     * @param string|null $description
     * @param string $dataId
     */
    public function __construct(ItemType $type, string $name, ?string $description, string $dataId)
    {
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->dataId = $dataId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ItemType
     */
    public function getType(): ItemType
    {
        return $this->type;
    }

    /**
     * @param ItemType $type
     * @return Item
     */
    public function setType(ItemType $type): Item
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Item
     */
    public function setName(string $name): Item
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     * @return Item
     */
    public function setDescription(?string $description): Item
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDataId(): string
    {
        return $this->dataId;
    }

    /**
     * @param string $dataId
     */
    public function setDataId(string $dataId): void
    {
        $this->dataId = $dataId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->toArray(),
            'name' => $this->name,
            'description' => $this->description,
            'data_id' => $this->dataId
        ];
    }
}