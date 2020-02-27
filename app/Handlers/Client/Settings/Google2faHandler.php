<?php


namespace App\Handlers\Client\Settings;


use App\Entity\Site\User;
use App\Events\Client\Settings\Google2faEvent;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

class Google2faHandler
{
    private $userRepository;

    private $google;

    public function __construct(UserRepository $userRepository, GoogleAuthenticator $google)
    {
        $this->userRepository = $userRepository;
        $this->google = $google;
    }

    public function generateSecret(): string
    {
        return $this->google->generateSecret();
    }

    public function enable(User $user, string $secret, string $code): void
    {
        if ($user->hasG2fa()) {
            throw new Exception('Гугл-Аутентификатор уже включен!');
        }

        if (!$this->google->checkCode($secret, $code)) {
            throw new Exception('Неправильный код!');
        }

        $user->setG2fa($secret);
        $this->userRepository->update($user);

        event(new Google2faEvent($user, true));
    }

    public function disable(User $user, string $code): void
    {
        if (!$user->hasG2fa()) {
            throw new Exception('Гугл-Аутентификатор еще не включен!');
        }

        if (!$this->google->checkCode($user->getG2fa(), $code)) {
            throw new Exception('Неправильный код!');
        }

        $user->setG2fa(null);
        $this->userRepository->update($user);

        event(new Google2faEvent($user, false));
    }
}