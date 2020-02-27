<?php


namespace App\Handlers\Api\Launcher;


use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;
use App\Services\Auth\Hasher\Hasher;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

class AuthProviderHandler
{
    private $userRepository;

    private $hasher;

    private $google;

    public function __construct(UserRepository $userRepository, Hasher $hasher, GoogleAuthenticator $google)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->google = $google;
    }

    public function getUser(string $name): User
    {
        $user = $this->userRepository->findByName($name);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    public function checkPassword(User $user, string $password): bool
    {
        return $this->hasher->check($password, $user->getPassword());
    }

    public function updateUser(User $user): void
    {
        $this->userRepository->update($user);
    }

    public function checkGoogle(User $user, string $code): bool
    {
        return $this->google->checkCode($user->getG2fa(), $code);
    }
}