<?php


namespace App\Entity\Site;


use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * BugReportMessage
 *
 * @ORM\Table(name="pr_bugreport_messages")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class BugReportMessage
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
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var DateTimeImmutable
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
     * @var BugReport
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\BugReport")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bugreport_id", referencedColumnName="id")
     * })
     */
    private $bugReport;

    /**
     * BugReportMessage constructor.
     *
     * @param BugReport $bugReport
     * @param User $user
     * @param string $message
     */
    public function __construct(BugReport $bugReport, User $user, string $message)
    {
        $this->bugReport = $bugReport;
        $this->user = $user;
        $this->message = $message;
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
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
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
     * @return BugReport
     */
    public function getBugReport(): BugReport
    {
        return $this->bugReport;
    }

    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function prePersist(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'user' => [
                'id' => $this->user->getId(),
                'name' => $this->user->getName()
            ],
            'created' => $this->createdAt->getTimestamp()
        ];
    }
}