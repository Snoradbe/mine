<?php


namespace App\Entity\Site\Shop;


use App\Entity\Site\Server;
use App\Exceptions\Exception;
use Doctrine\ORM\Mapping as ORM;

/**
 * Shop\Category
 *
 * @ORM\Table(name="pr_shop_categories")
 * @ORM\Entity
 */
class Category
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
     * @var Category|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Shop\Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_category_id", referencedColumnName="id")
     * })
     */
    private $parentCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer")
     */
    private $weight;

    /**
     * Category constructor.
     *
     * @param Server|null $server
     * @param Category|null $parentCategory
     * @param string $name
     * @param int $weight
     */
    public function __construct(?Server $server, ?Category $parentCategory, string $name, int $weight)
    {
        $this->server = $server;
        $this->parentCategory = $parentCategory;
        $this->name = $name;
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return Category
     */
    public function setName(string $name): Category
    {
        $this->name = $name;

        return $this;
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
     * @return Category
     */
    public function setServer(?Server $server): Category
    {
        $this->server = $server;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getParentCategory(): ?Category
    {
        return $this->parentCategory;
    }

    /**
     * @param Category|null $parentCategory
     */
    public function setParentCategory(?Category $parentCategory): void
    {
        $this->parentCategory = $parentCategory;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     * @return Category
     */
    public function setWeight(int $weight): Category
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @param bool $isChild - от зацикливания (дочерняя категория может иметь только 1 родителя [Magic -> Blocks -> Divine...])
     * @return array
     * @throws Exception
     */
    public function toArray(bool $isChild = false): array
    {
        if ($isChild && !is_null($this->parentCategory)) {
            throw new Exception('Child category have another child category!');
        }

        return [
            'id' => $this->id,
            'server' => is_null($this->server) ? null : $this->server->toArray(),
            'parent' => is_null($this->parentCategory) ? null : $this->parentCategory->toArray(true),
            'name' => $this->name,
            'weight' => $this->weight
        ];
    }
}