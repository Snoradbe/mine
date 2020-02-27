<?php


namespace App\Services\Auth;


use App\Services\Auth\Session\Session;
use App\Services\Auth\Session\SessionCookie;

class DefaultAuthService implements AuthService
{
    private $sessionCookie;

    /**
     * @var Session
     */
    private $session;

    public function __construct(SessionCookie $sessionCookie)
    {
        $this->sessionCookie = $sessionCookie;
    }

    public function check(): bool
    {
        $this->initSession();

        return $this->session->check();
    }

    private function initSession(): void
    {
        if (is_null($this->session)) {
            $this->session = $this->sessionCookie->createFromCookie();
            //var_dump($this->session->getUser()->getName());
        }
    }
}