<?php


namespace App\Services\Auth\Session\Driver;


use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;

class CookieDriver implements Driver
{
    private $request;

    private $cookie;

    public function __construct(Request $request, CookieJar $cookie)
    {
        $this->request = $request;
        $this->cookie = $cookie;
    }

    public function getUserId(): ?int
    {
        $id = $this->request->cookie('dle_user_id', '');

        return empty($id) ? null : (int) $id;
    }

    public function getHashedPassword(): ?string
    {
        return $this->request->cookie('dle_password');
    }

    public function forget(): void
    {
        $this->cookie->queue($this->cookie->forget('dle_user_id'));
        $this->cookie->queue($this->cookie->forget('dle_password'));
    }
}