<?php


namespace App\Entity\Site;


use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Vaucher
 *
 * @ORM\Table(name="pr_vauchers_users")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class VaucherUser
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
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $date;

    /**
     * @var Vaucher
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Vaucher")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vaucher_id", referencedColumnName="id")
     * })
     */
    private $vaucher;

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
     * VaucherUser constructor.
     *
     * @param Vaucher $vaucher
     * @param User $user
     */
    public function __construct(Vaucher $vaucher, User $user)
    {
        $this->vaucher = $vaucher;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return Vaucher
     */
    public function getVaucher(): Vaucher
    {
        return $this->vaucher;
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
    public function prePerist(): void
    {
        $this->date = new DateTimeImmutable();
    }
}