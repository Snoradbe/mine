<?php


namespace App\Entity\Site;


use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * BugReport
 *
 * @ORM\Table(name="pr_bugreport")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class BugReport
{
    public const IS_ACTIVE = [
        'type' => 0,
        'desc' => 'На рассмотрении'
    ];
    public const ACCEPT = [
        'type' => 1,
        'desc' => 'Исправлено'
    ];
    public const CANCEL = [
        'type' => 2,
        'desc' => 'Отклонено / Не может быть исправлено'
    ];

    public const TYPES = [
        self::IS_ACTIVE,
        self::ACCEPT,
        self::CANCEL,
    ];

    public const SMALL_BUG = [
        'type' => 1,
        'desc' => 'Небольшой недочет'
    ];
    public const BUG = [
        'type' => 2,
        'desc' => 'Ошибка'
    ];
    public const HIGH_BUG = [
        'type' => 3,
        'desc' => 'Серьезная ошибка'
    ];
    public const DUPE = [
        'type' => 4,
        'desc' => 'Дюп'
    ];

    public const BUGS = [
        self::SMALL_BUG,
        self::BUG,
        self::HIGH_BUG,
        self::DUPE,
    ];

    public static function getStatusName(int $type): string
    {
        $find = array_filter(static::TYPES, function (array $data) use ($type) {
            return $data['type'] == $type;
        });

        if (!empty($find) && is_array($find)) {
            return array_values($find)[0]['desc'];
        }

        return '';
    }

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
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_read", type="boolean", nullable=false)
     */
    private $read = false;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status = 0;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(name="updated_at", type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_user", referencedColumnName="user_id")
     * })
     */
    private $lastUser;

    /**
     * @var Server
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Server")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="server_id", referencedColumnName="id")
     * })
     */
    private $server;

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
     * BugReport constructor.
     *
     * @param User $user
     * @param Server $server
     * @param int $type
     * @param string $title
     * @param string $message
     */
    public function __construct(User $user, Server $server, int $type, string $title, string $message)
    {
        $this->user = $user;
        $this->lastUser = $user;
        $this->server = $server;
        $this->type = $type;
        $this->title = $title;
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
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getShortTitle(): string
    {
        if (mb_strlen($this->title, 'UTF-8') > 20) {
            return mb_substr(strip_tags($this->title), 0, 20, 'UTF-8');
        }

        return $this->title;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->read;
    }

    /**
     * @param bool $read
     */
    public function setRead(bool $read): void
    {
        $this->read = $read;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status == static::IS_ACTIVE;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return User
     */
    public function getLastUser(): User
    {
        return $this->lastUser;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
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
    public function prePersist(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @ORM\PreUpdate
     * @throws \Exception
     */
    public function preUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->status,
            'created' => $this->getCreatedAt()->getTimestamp(),
            'updated' => is_null($this->updatedAt) ? null : $this->updatedAt->getTimestamp()
        ];
    }
}