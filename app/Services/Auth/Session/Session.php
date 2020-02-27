<?php


namespace App\Services\Auth\Session;


use App\Entity\Site\User;

class Session
{
    private $user;

    public function __construct(?User $user)
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function check(): bool
    {
        return !is_null($this->user);
    }
}