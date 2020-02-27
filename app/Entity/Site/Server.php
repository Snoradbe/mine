<?php


namespace App\Entity\Site;


use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Server
 *
 * @ORM\Table(name="pr_servers")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Server
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
     * @ORM\Column(name="name", type="string", length=36, nullable=false)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled = false;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=false)
     */
    private $ip;

    /**
     * @var int
     *
     * @ORM\Column(name="port", type="integer", nullable=false)
     */
    private $port;

    /**
     * @var int
     *
     * @ORM\Column(name="rcon_port", type="integer", nullable=false)
     */
    private $rconPort;

    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(name="wipe", type="datetime_immutable", nullable=true)
     */
    private $wipe;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * Server constructor.
     *
     * @param string $name
     * @param string $ip
     * @param int $port
     * @param int $rconPort
     */
    public function __construct(string $name, string $ip, int $port, int $rconPort)
    {
        $this->name = $name;
        $this->ip = $ip;
        $this->port = $port;
        $this->rconPort = $rconPort;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return int
     */
    public function getRconPort(): int
    {
        return $this->rconPort;
    }

    /**
     * @param int $rconPort
     */
    public function setRconPort(int $rconPort): void
    {
        $this->rconPort = $rconPort;
    }

    /**
     * @return string
     */
    public function getRconPassword(): string
    {
        return config('site.game.rcon.server_' . $this->id, '');
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getWipe(): ?DateTimeImmutable
    {
        return $this->wipe;
    }

    /**
     * @param DateTimeImmutable|null $wipe
     */
    public function setWipe(?DateTimeImmutable $wipe): void
    {
        $this->wipe = $wipe;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getConnectionName(): string
    {
        return 'server_' . $this->id;
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
     * @param bool $secret
     * @return array
     */
    public function toArray(bool $secret = false): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'Server#' . $this->id;
    }
}