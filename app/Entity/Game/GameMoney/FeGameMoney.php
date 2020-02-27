<?php


namespace App\Entity\Game\GameMoney;


use Doctrine\ORM\Mapping as ORM;

/**
 * FeGameMoney
 *
 * @ORM\Table(name="money")
 * @ORM\Entity
 */
class FeGameMoney implements GameMoney
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
     * @ORM\Column(name="username", type="string", length=24, nullable=false)
     */
    private $username;

    /**
     * @var int
     *
     * @ORM\Column(name="balance", type="decimal", nullable=false)
     */
    private $balance;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getMoney(): float
    {
        return $this->balance;
    }

    /**
     * @param float $money
     */
    public function setMoney(float $money): void
    {
        $this->balance = $money;
    }
}