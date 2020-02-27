<?php


namespace App\Entity\Site;


use Doctrine\ORM\Mapping as ORM;

/**
 * TopLastVotes
 * Ежемесячные итоги топа голосующих
 *
 * @ORM\Table(name="top_last_votes")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class TopLastVotes
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
     * @var int
     *
     * @ORM\Column(name="votes", type="smallint", nullable=false)
     */
    private $votes;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="smallint", nullable=false)
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="reward", type="integer", nullable=false)
     */
    private $reward;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    private $user;

    /**
     * TopLastVotes constructor.
     * @param User $user
     * @param int $votes
     * @param int $position
     * @param int $reward
     */
    public function __construct(User $user, int $votes, int $position, int $reward)
    {
        $this->user = $user;
        $this->votes = $votes;
        $this->position = $position;
        $this->reward = $reward;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getVotes(): int
    {
        return $this->votes;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getReward(): int
    {
        return $this->reward;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}