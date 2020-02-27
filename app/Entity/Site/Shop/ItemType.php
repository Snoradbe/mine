<?php


namespace App\Entity\Site\Shop;


use Doctrine\ORM\Mapping as ORM;

/**
 * Shop\ItemType
 *
 * @ORM\Table(name="pr_shop_item_types")
 * @ORM\Entity
 */
class ItemType
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="distributor", type="string")
     */
    private $distributor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="extra", type="text")
     */
    private $extra;

    /**
     * @var array
     */
    private $extraArray;

    /**
     * Type constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $distributor
     * @param array $extra
     */
    public function __construct(string $id, string $name, string $distributor, array $extra)
    {
        $this->id = $id;
        $this->name = $name;
        $this->distributor = $distributor;
        $this->setExtra($extra);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
     * @return ItemType
     */
    public function setName(string $name): ItemType
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDistributor(): string
    {
        return $this->distributor;
    }

    /**
     * @param string $distributor
     * @return ItemType
     */
    public function setDistributor(string $distributor): ItemType
    {
        $this->distributor = $distributor;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExtra(): ?string
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     */
    public function setExtra(array $extra): void
    {
        $this->extra = empty($extra) ? null : json_encode($extra);
        $this->extraArray = $extra;
    }

    /**
     * @return array
     */
    public function getExtraArray(): array
    {
        if (is_null($this->extraArray)) {
            $this->extraArray = is_null($this->extra) ? [] : json_decode($this->extra, true);
        }

        return $this->extraArray;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'distributor' => $this->distributor,
            'extra' => $this->getExtraArray()
        ];
    }
}