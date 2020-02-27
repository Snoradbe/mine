<?php


namespace App\Services\Cabinet\Vaucher;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Entity\Site\VaucherUser;
use App\Exceptions\Exception;
use App\Repository\Site\Vaucher\VaucherRepository;
use App\Repository\Site\VaucherUser\VaucherUserRepository;

class VaucherManager
{
    private $vaucherRepository;

    private $vaucherUserRepository;

    public function __construct(VaucherRepository $vaucherRepository, VaucherUserRepository $vaucherUserRepository)
    {
        $this->vaucherRepository = $vaucherRepository;
        $this->vaucherUserRepository = $vaucherUserRepository;
    }

    private function find(string $code): ?Vaucher
    {
        return $this->vaucherRepository->findByCode($code);
    }

    private function check(User $user, Vaucher $vaucher): bool
    {
        return is_null($this->vaucherUserRepository->findByUserVaucher($user, $vaucher));
    }

    public function activateByCode(User $user, string $code): string
    {
        $vaucher = $this->find($code);
        if (is_null($vaucher)) {
            throw new Exception('Неверный код!');
        }

        if (!$vaucher->isActive()) {
            throw new Exception('Код уже не активен');
        }

        if (!$this->check($user, $vaucher)) {
            throw new Exception('Вы уже активировали этот код!');
        }

        return $this->activate($user, $vaucher);
    }

    private function activate(User $user, Vaucher $vaucher): string
    {
        $vaucher->activate();

        $type = config('site.vauchers.type.' . $vaucher->getType());
        if (empty($type) || !is_array($type)) {
            throw new Exception("Vaucher type `{$vaucher->getType()}` is not found!");
        }

        $handler = $type['handler'];
        $message = $type['message'];
        if (!is_null($vaucher->getMessage())) {
            $message = $vaucher->getMessage();
        }

        $message = app()->make($handler)->handle($user, $vaucher, $message);

        $this->vaucherRepository->update($vaucher);
        $this->vaucherUserRepository->create(new VaucherUser($vaucher, $user));

        return $message;
    }
}