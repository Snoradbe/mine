<?php


namespace App\Entity\Game\HawkEye;


use Doctrine\ORM\Mapping as ORM;

/**
 * HawkPlayer
 *
 * @ORM\Table(name="hawk_players", uniqueConstraints={@ORM\UniqueConstraint(name="player", columns={"player"})})
 * @ORM\Entity
 */
class HawkPlayer
{
    /**
     * @var int
     *
     * @ORM\Column(name="player_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $playerId;

    /**
     * @var string
     *
     * @ORM\Column(name="player", type="string", length=40, nullable=false)
     */
    private $player;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->player;
    }
}