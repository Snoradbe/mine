<?php


namespace App\Events\Admin\Servers;


use App\Entity\Site\Server;
use App\Events\Event;

/**
 * Вызывается перед удалением сервера
 *
 * Class BeforeDeleteServerEvent
 * @package App\Events\Admin\Servers
 */
class BeforeDeleteServerEvent implements Event
{
    /**
     * @var Server
     */
    private $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}