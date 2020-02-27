<?php


namespace App\Entity\Game\LiteBans;


use App\Entity\Site\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * LiteBansBan
 *
 * @ORM\Table(name="litebans_bans", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})}, indexes={@ORM\Index(name="idx_litebans_bans_uuid", columns={"uuid"}), @ORM\Index(name="idx_litebans_bans_banned_by_uuid", columns={"banned_by_uuid"}), @ORM\Index(name="idx_litebans_bans_until", columns={"until"}), @ORM\Index(name="idx_litebans_bans_ip", columns={"ip"}), @ORM\Index(name="idx_litebans_bans_time", columns={"time"}), @ORM\Index(name="idx_litebans_bans_active", columns={"active"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class LiteBansBan
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", length=45, nullable=true)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=2048, nullable=false)
     */
    private $reason;

    /**
     * @var string|null
     *
     * @ORM\Column(name="banned_by_uuid", type="string", length=36, nullable=true)
     */
    private $bannedByUuid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="banned_by_name", type="string", length=128, nullable=true)
     */
    private $bannedByName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="removed_by_uuid", type="string", length=36, nullable=true)
     */
    private $removedByUuid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="removed_by_name", type="string", length=128, nullable=true)
     */
    private $removedByName;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="removed_by_date", type="datetime_immutable", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $removedByDate = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="time", type="bigint", nullable=false)
     */
    private $time;

    /**
     * @var int
     *
     * @ORM\Column(name="until", type="bigint", nullable=false)
     */
    private $until;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = true;

    /**
     * @var LiteBansHistory
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Game\LiteBans\LiteBansHistory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="uuid", referencedColumnName="uuid")
     * })
     */
    private $name;

    /**
     * LiteBansBan constructor.
     *
     * @param User $admin
     * @param string $uuid
     * @param string $reason
     * @param int $until
     */
    public function __construct(User $admin, string $uuid, string $reason, int $until)
    {
        $this->uuid = $uuid;
        $this->bannedByUuid = $admin->getUuid();
        $this->bannedByName = $admin->getName();
        $this->reason = $reason;
        $this->setUntil($until);
        $this->active = true;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return (int) $this->time / 1000;
    }

    /**
     * @param bool $toTimeStamp
     * @return int
     */
    public function getUntil(bool $toTimeStamp = false): int
    {
        if ($this->until < 1) {
            return 0;
        }

        return !$toTimeStamp ? $this->until : (int) ($this->until / 1000);
    }

    /**
     * @param int $until
     */
    public function setUntil(int $until): void
    {
        $this->until = $until > 0 ? ($until * 1000) : 0;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name->getName();
    }

    /**
     * @return string
     */
    public function getAdmin(): string
    {
        return $this->bannedByName;
    }

    /**
     * @return string
     */
    public function getAdminUuid(): string
    {
        return $this->bannedByUuid;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param User|null $admin
     */
    public function unban(?User $admin): void
    {
        if (!is_null($admin)) {
            $this->removedByName = $admin->getName();
            $this->removedByUuid = $admin->getUuid();
        } else {
            $this->removedByName = 'CONSOLE';
            $this->removedByUuid = 'CONSOLE';
        }
        $this->removedByDate = new \DateTimeImmutable();
        $this->active = false;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->time = time() * 1000;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'admin' => $this->getAdmin(),
            'reason' => $this->getReason(),
            'date' => $this->getTime(),
            'until' => $this->getUntil(true)
        ];
    }
}