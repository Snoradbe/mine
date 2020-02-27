<?php


namespace App\Events\Admin\Schematics;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class UploadSchematicEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $size;

    /**
     * UploadSchematicEvent constructor.
     * @param User $admin
     * @param Server $server
     * @param string $name
     * @param int $size
     */
    public function __construct(User $admin, Server $server, string $name, int $size)
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->name = $name;
        $this->size = $size;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'size' => $this->size
        ];
    }
}