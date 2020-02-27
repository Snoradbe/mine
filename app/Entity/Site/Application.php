<?php


namespace App\Entity\Site;


use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Application
 *
 * @ORM\Table(name="pr_applications")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Application
{
    public const WAIT = 0;
    public const ACCEPT = 1;
    public const CANCEL = 2;
    public const AGAIN = 3;

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
     * @ORM\Column(name="form", type="text", nullable=false)
     */
    private $form;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255, nullable=false)
     */
    private $position;

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
     * @var array
     */
    private $formArray;

    /**
     * Application constructor.
     *
     * @param Server $server
     * @param User $user
     * @param array $form
     * @param string $position
     */
    public function __construct(Server $server, User $user, array $form, string $position)
    {
        $this->server = $server;
        $this->user = $user;
        $this->setForm($form);
        $this->position = $position;
    }

    /**
     * @param int $status
     * @return string
     */
    public static function getStatusName(int $status): string
    {
        switch ($status)
        {
            case static::WAIT: return 'на рассмотрении';
            case static::ACCEPT: return 'принята';
            case static::CANCEL: return 'отклонена';
            case static::AGAIN: return 'на повтор';

            default: return $status . ' (???)';
        }
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
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
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
     * @return string
     */
    public function getForm(): string
    {
        return $this->form;
    }

    /**
     * @param array $form
     */
    public function setForm(array $form): void
    {
        $this->form = json_encode($form);
        $this->formArray = $form;
    }

    /**
     * @return array
     */
    public function getFormArray(): array
    {
        if (is_null($this->formArray)) {
            $this->formArray = (array) json_decode($this->form, true);
        }

        return $this->formArray;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
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
     * @param int $cooldown
     * @return bool
     */
    public function canAgain(int $cooldown): bool
    {
        if (
            $this->status == static::AGAIN
            ||
            (
                $this->status != static::WAIT && $this->createdAt->getTimestamp() + ($cooldown + 86400) < time()
            )
        ) {
            return true;
        }

        return false;
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
     * @param int $cooldown
     * @return array
     */
    public function toArray(int $cooldown = 0): array
    {
        return [
            'user' => $this->user->toSimpleArray(),
            'server' => $this->server->toArray(),
            'form' => empty($this->form) ? [] : json_decode($this->form, true),
            'status' => $this->status,
            'position' => $this->position,
            'created' => $this->createdAt->getTimestamp(),
            'updated' => is_null($this->updatedAt) ? null : $this->updatedAt->getTimestamp(),
            'can_again' => $this->canAgain($cooldown)
        ];
    }
}