<?php


namespace App\Events\Client;


use App\Entity\Site\User;

abstract class ClientEvent
{
    /**
     * @var User
     */
    protected $user;

    /**
     * ClientEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->user->getLastLoginIP();
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}