<?php


namespace App\Entity\Site;


use Doctrine\ORM\Mapping as ORM;

/**
 * VoteLog
 *
 * @ORM\Table(name="pr_vote_logs")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class VoteLog
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
     * @ORM\Column(name="top", type="string", length=20, nullable=false)
     */
    private $top;

    /**
     * @var string
     *
     * @ORM\Column(name="vote_day", type="string", length=10, nullable=false)
     */
    private $voteDay;

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
     * VoteLog constructor.
     *
     * @param User $user
     * @param string $top
     */
    public function __construct(User $user, string $top)
    {
        $this->user = $user;
        $this->top = $top;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getTop(): string
    {
        return $this->top;
    }

    /**
     * @return string
     */
    public function getVoteDay(): string
    {
        return $this->voteDay;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function prePersist(): void
    {
        $this->voteDay = date('Y-m-d');
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user' => [
                'id' => $this->user->getId(),
                'name' => $this->user->getName()
            ],
            'top' => $this->top,
            'date' => $this->createdAt->getTimestamp()
        ];
    }
}