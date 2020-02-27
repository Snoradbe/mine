<?php


namespace App\Events\Admin\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;
use App\Services\Cabinet\Prefix\PrefixSuffix;

class ChangePrefixEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var User
     */
    private $target;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var PrefixSuffix
     */
    private $prefix;

    /**
     * ChangePrefixEvent constructor.
     * @param User $admin
     * @param User $target
     * @param Server $server
     * @param PrefixSuffix $prefix
     */
    public function __construct(User $admin, User $target, Server $server, PrefixSuffix $prefix)
    {
        $this->admin = $admin;
        $this->target = $target;
        $this->server = $server;
        $this->prefix = $prefix;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return User
     */
    public function getTarget(): User
    {
        return $this->target;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return PrefixSuffix
     */
    public function getPrefix(): PrefixSuffix
    {
        return $this->prefix;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'target' => [
                'id' => $this->target->getId(),
                'name' => $this->target->getName(),
                'uuid' => $this->target->getUuid()
            ],
            'prefix' => $this->prefix->toArray()
        ];
    }
}