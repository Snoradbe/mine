<?php


namespace App\Services\Game\Rcon\Entity;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * RconLog
 *
 * @ORM\Table(name="site_rcon_log")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */

class RconLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="string", length=255, nullable=false)
     */
    private $command;

    /**
     * @var string|null
     *
     * @ORM\Column(name="response", type="text", length=65000, nullable=true)
     */
    private $response;

    /**
     * @var Server
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Server")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="server_id", referencedColumnName="id")
     * })
     */
    private $server;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="date", type="datetime_immutable", nullable=true)
     */
    private $date;

    /**
     * RconLog constructor.
     *
     * @param User $user
     * @param Server $server
     * @param string $command
     * @param null|string $response
     */
    public function __construct(User $user, Server $server, string $command, ?string $response)
    {
        $this->user = $user;
        $this->server = $server;
        $this->command = $command;
        $this->response = $response;
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
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return null|string
     */
    public function getResponse(): ?string
    {
        return $this->response;
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
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @ORM\PrePersist
     */
    public function generateDate(): void
    {
        $this->date = new \DateTimeImmutable();
    }
}