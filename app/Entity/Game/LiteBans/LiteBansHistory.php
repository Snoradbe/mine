<?php


namespace App\Entity\Game\LiteBans;


use Doctrine\ORM\Mapping as ORM;

/**
 * LiteBansHistory
 *
 * @ORM\Table(name="litebans_history", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})}, indexes={@ORM\Index(name="idx_litebans_history_uuid", columns={"uuid"}), @ORM\Index(name="idx_litebans_history_ip", columns={"ip"}), @ORM\Index(name="idx_litebans_history_name", columns={"name"})})
 * @ORM\Entity
 */
class LiteBansHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=16, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=false)
     * @ORM\Id
     */
    private $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}