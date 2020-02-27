<?php


namespace App\Entity\Game\HawkEye;


use Doctrine\ORM\Mapping as ORM;

/**
 * Hawkeye
 *
 * @ORM\Table(name="hawkeye", indexes={@ORM\Index(name="player", columns={"player_id"}), @ORM\Index(name="world_id", columns={"world_id"}), @ORM\Index(name="timestamp", columns={"timestamp"}), @ORM\Index(name="action", columns={"action"}), @ORM\Index(name="x_y_z", columns={"x", "y", "z"})})
 * @ORM\Entity
 */
class HawkLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="data_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $dataId;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="timestamp", type="datetime_immutable", nullable=false)
     */
    private $timestamp;

    /**
     * @var int
     *
     * @ORM\Column(name="action", type="integer", nullable=false)
     */
    private $action;

    /**
     * @var int
     *
     * @ORM\Column(name="world_id", type="integer", nullable=false)
     */
    private $worldId;

    /**
     * @var int
     *
     * @ORM\Column(name="x", type="integer", nullable=false)
     */
    private $x;

    /**
     * @var int
     *
     * @ORM\Column(name="y", type="integer", nullable=false)
     */
    private $y;

    /**
     * @var int
     *
     * @ORM\Column(name="z", type="integer", nullable=false)
     */
    private $z;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data", type="string", length=500, nullable=true)
     */
    private $data;

    /**
     * @var HawkPlayer
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Game\HawkEye\HawkPlayer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="player_id", referencedColumnName="player_id")
     * })
     */
    private $hawkPlayer;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->hawkPlayer->getName();
    }

    /**
     * @return int
     */
    public function getAction(): int
    {
        return $this->action;
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getZ(): int
    {
        return $this->z;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getWorldId(): int
    {
        return $this->worldId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->dataId,
            'name' => $this->getName(),
            'action' => $this->action,
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'data' => $this->data,
            'date' => $this->timestamp->getTimestamp()
        ];
    }
}