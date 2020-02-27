<?php


namespace App\Entity\Site;


use Doctrine\ORM\Mapping as ORM;

/**
 * Discount
 *
 * @ORM\Table(name="pr_discounts")
 * @ORM\Entity
 */
class Discount
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
     * @ORM\Column(name="type", type="string", length=32, nullable=false, options={"comment"="all, module_[cabinet,shop...], group_[vip...]"})
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="discount", type="integer", nullable=false, options={"comment"="1%-99%"})
     */
    private $discount;

    /**
     * @var int
     *
     * @ORM\Column(name="time_end", type="integer", nullable=false)
     */
    private $timeEnd;

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
     * Discount constructor.
     * @param Server|null $server
     * @param string $type
     * @param int $discount
     * @param int $timeEnd
     */
    public function __construct(?Server $server, string $type, int $discount, int $timeEnd)
    {
        $this->server = $server;
        $this->type = $type;
        $this->discount = $discount;
        $this->timeEnd = $timeEnd;
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->server;
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
    public function getTimeEnd(): int
    {
        return $this->timeEnd;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'server' => is_null($this->server) ? null : $this->server->toArray(),
            'discount' => $this->discount,
            'end' => $this->timeEnd
        ];
    }
}