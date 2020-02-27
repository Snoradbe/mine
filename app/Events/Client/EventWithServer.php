<?php


namespace App\Events\Client;


use App\Entity\Site\Server;

trait EventWithServer
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }
}