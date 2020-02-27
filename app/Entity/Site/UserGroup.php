<?php


namespace App\Entity\Site;


use App\Exceptions\Exception;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserGroup
 *
 * @ORM\Table(name="pr_user_groups", indexes={@ORM\Index(name="pr_user_groups_server_id_foreign", columns={"server_id"}), @ORM\Index(name="pr_user_groups_user_id_foreign", columns={"user_id"})})
 * @ORM\Entity
 */
class UserGroup
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
     * @ORM\Column(name="created_at", type="integer", nullable=false)
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="expire_at", type="integer", nullable=false)
     */
    private $expireAt;

    /**
     * @var Group
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * })
     */
    private $group;

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
     * UserGroup constructor.
     *
     * @param User $user
     * @param Server $server
     * @param Group $group
     * @param int $expireAt
     */
    public function __construct(User $user, Server $server, Group $group, int $expireAt)
    {
        $this->user = $user;
        $this->server = $server;
        $this->group = $group;
        $this->expireAt = $expireAt;

        $this->createdAt = time();
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
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getExpireAt(): int
    {
        return $this->expireAt;
    }

    /**
     * @param int $expireAt
     */
    public function setExpireAt(int $expireAt): void
    {
        $this->expireAt = $expireAt;
    }

    /**
     * @return bool
     */
    public function isExpire(): bool
    {
        return $this->expireAt != 0 && $this->expireAt < time();
    }

    /**
     * @param int $days
     * @throws \Exception
     */
    public function extendTime(int $days): void
    {
        if ($this->expireAt == 0) {
            throw new Exception('Эта группа выдана навсегда, поэтому ее нельзя продлить!');
        }

        $this->expireAt += (86400) * $days;
    }

    /**
     * @param bool $secret
     * @return array
     */
    public function toArray(bool $secret = true): array
    {
        return [
            'user' => $this->user->toSimpleArray(),
            'group' => $this->group->toArray($secret),
            'server' => is_null($this->server) ? null : $this->server->toArray(),
            'created' => $this->createdAt,
            'expired' => $this->expireAt
        ];
    }
}