<?php


namespace App\Entity\Game\PermissionsEx;


use Doctrine\ORM\Mapping as ORM;

/**
 * Permission
 *
 * @ORM\Table(name="permissions")
 * @ORM\Entity
 */
class Permission
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type; //0 - группа, 1 - игрок

    /**
     * @var string
     *
     * @ORM\Column(name="permission", type="text", length=50, nullable=false)
     */
    private $permission;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world", type="string", length=50, nullable=true)
     */
    private $world = ''; //ставить '', а не null

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * Permission constructor.
     *
     * @param string $name
     * @param bool $type
     * @param string $permission
     * @param string|null $world
     * @param string|null $value
     */
    public function __construct(string $name, bool $type, string $permission, ?string $world = null, ?string $value = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->permission = $permission;
        $this->world = is_null($world) ? '' : $world;
        $this->value = $value;
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
     * @return string
     */
    public function getPermission(): string
    {
        return $this->permission;
    }

    /**
     * @return bool
     */
    public function isType(): bool
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getWorld(): ?string
    {
        return $this->world;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }
}