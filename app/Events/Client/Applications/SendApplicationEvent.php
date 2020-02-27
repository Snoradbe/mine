<?php


namespace App\Events\Client\Applications;


use App\Entity\Site\Application;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Client\EventWithServer;
use App\Events\Event;

class SendApplicationEvent extends ClientEvent implements Event
{
    use EventWithServer;

    /**
     * @var Application
     */
    private $application;

    /**
     * SendApplicationEvent constructor.
     * @param User $user
     * @param Server $server
     * @param Application $application
     */
    public function __construct(User $user, Server $server, Application $application)
    {
        parent::__construct($user);

        $this->server = $server;
        $this->application = $application;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'application' => [
                'id' => $this->application->getId(),
                'position' => $this->application->getPosition()
            ],
        ];
    }
}