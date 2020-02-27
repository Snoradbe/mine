<?php


namespace App\Services\Auth;


use App\Entity\Site\User;

class Auth
{
    private function __construct()
    {
        //static class
    }

    /**
     * @var User|null
     */
    private static $user;

    /**
     * @return User|null
     */
    public static function getUser(): ?User
    {
        return self::$user;
    }

    /**
     * @param User $user
     */
    public static function setUser(User $user): void
    {
        self::$user = $user;
    }

    /**
     * @return bool
     */
    public static function isLogged(): bool
    {
        return !is_null(self::$user);
    }
}