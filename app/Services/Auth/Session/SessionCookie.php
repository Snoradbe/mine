<?php


namespace App\Services\Auth\Session;


use App\Repository\Site\User\UserRepository;
use App\Services\Auth\Auth;
use App\Services\Auth\Hasher\Hasher;
use App\Services\Auth\Session\Driver\Driver;

class SessionCookie
{
    private $userRepository;

    private $driver;

    private $hasher;

    public function __construct(UserRepository $userRepository, Driver $driver, Hasher $hasher)
    {
        $this->userRepository = $userRepository;
        $this->driver = $driver;
        $this->hasher = $hasher;
    }

    public function createFromCookie(): Session
    {
        $userId = $this->driver->getUserId();
        $password = $this->driver->getHashedPassword();

        if (empty($userId) || empty($password)) {
            return $this->createEmpty();
        }

        $user = $this->userRepository->find($userId);
        if (is_null($user)) {
            return $this->createEmpty();
        }

        if (!$this->hasher->check($password, $user->getPassword())) {
            return $this->createEmpty();
        }

        //var_dump($user->getName());

        Auth::setUser($user);

        //var_dump(Auth::getUser()->getName());

        return new Session($user);
    }

    private function createEmpty(): Session
    {
        return new Session(null);
    }
}