<?php


namespace App\Entity\Game\PermissionsEx;


use Doctrine\ORM\Mapping as ORM;

/**
 * PermissionInheritance
 *
 * @ORM\Table(name="permissions_inheritance")
 * @ORM\Entity
 */
class PermissionInheritance
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
     * @ORM\Column(name="child", type="string", length=50, nullable=false)
     */
    private $child;

    /**
     * @var string
     *
     * @ORM\Column(name="parent", type="text", length=50, nullable=false)
     */
    private $parent;

    /**
     * @var bool
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type; //0 - группа, 1 - игрок

    /**
     * @var string|null
     *
     * @ORM\Column(name="world", type="string", length=50, nullable=true)
     */
    private $world; //только null, '' не выдаст права!

    /**
     * PermissionInheritance constructor.
     *
     * @param string $child
     * @param string $parent
     * @param int $type
     * @param string|null $world
     */
    public function __construct(string $child, string $parent, int $type, ?string $world = null)
    {
        $this->child = $child;
        $this->parent = $parent;
        $this->type = $type;
        $this->world = $world;
    }
}