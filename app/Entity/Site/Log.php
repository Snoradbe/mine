<?php


namespace App\Entity\Site;


use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Table(name="pr_logs", indexes={@ORM\Index(name="pr_logs_server_id_foreign", columns={"server_id"}), @ORM\Index(name="pr_logs_user_id_foreign", columns={"user_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Log
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
     * @ORM\Column(name="type", type="smallint", nullable=false, options={"comment"="config: site.logs.types"})
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data", type="text", length=65535, nullable=true, options={"comment"="Дополнительная информация в json формате"})
     */
    private $data;

    /**
     * @var int|null
     *
     * @ORM\Column(name="spent", type="smallint", nullable=true, options={"comment"="Расход"})
     */
    private $spent;

    /**
     * @var int|null
     *
     * @ORM\Column(name="received", type="smallint", nullable=true, options={"comment"="Пополнение"})
     */
    private $received;

    /**
     * @var string|null
     *
     * @ORM\Column(name="valute", type="string", length=12, nullable=true)
     */
    private $valute;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=false)
     */
    private $ip;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @var Server|null
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
    private $arrayData;

    /**
     * Log constructor.
     *
     * @param User $user
     * @param Server|null $server
     * @param int $type
     * @param string $ip
     * @param array $data
     * @param int|null $spent
     * @param int|null $received
     */
    public function __construct(User $user, ?Server $server, int $type, string $ip, array $data = [], ?int $spent = null, ?int $received = null, ?string $valute = null)
    {
        $this->user = $user;
        $this->server = $server;
        $this->type = $type;
        $this->arrayData = $data;
        $this->ip = $ip;
        $this->data = empty($data) ? null : json_encode($data);
        $this->spent = $spent;
        $this->received = $received;
        $this->valute = $valute;
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
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getArrayData(): array
    {
        if (is_null($this->arrayData)) {
            $this->arrayData = empty($this->data) ? [] : (array) json_decode($this->data, true);
        }

        return $this->arrayData;
    }

    /**
     * @return int|null
     */
    public function getSpent(): ?int
    {
        return $this->spent;
    }

    /**
     * @return int|null
     */
    public function getReceived(): ?int
    {
        return $this->received;
    }

    /**
     * @return string|null
     */
    public function getValute(): ?string
    {
        return $this->valute;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
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
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @param bool $secretData
     * @return array
     */
    public function toArray(bool $secretData = false): array
    {
        return [
            'id' => $this->id,
            'name' => config('sitelogs.types.' . $this->type, '?'),
            'user' => $secretData ? $this->user->toArray() : $this->user->toSimpleArray(),
            'server' => is_null($this->server) ? null : $this->server->toArray($secretData),
            'type' => $this->type,
            'ip' => $this->ip,
            'data' => $this->getArrayData(),
            'spent' => $this->spent,
            'received' => $this->received,
            'valute' => $this->valute,
            'date' => $this->createdAt->getTimestamp()
        ];
    }
}