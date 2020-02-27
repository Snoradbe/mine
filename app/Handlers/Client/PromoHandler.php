<?php


namespace App\Handlers\Client;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Entity\Site\VaucherUser;
use App\Events\Client\Promo\UsePromoEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Vaucher\VaucherRepository;
use App\Repository\Site\VaucherUser\VaucherUserRepository;

class PromoHandler
{
    private $vaucherRepository;

    private $vaucherUserRepository;

    public function __construct(VaucherRepository $vaucherRepository, VaucherUserRepository $vaucherUserRepository)
    {
        $this->vaucherRepository = $vaucherRepository;
        $this->vaucherUserRepository = $vaucherUserRepository;
    }

    private function getVaucher(string $code): Vaucher
    {
        $vaucher = $this->vaucherRepository->findByCode($code);
        if (is_null($vaucher)) {
            throw new Exception('Код не найден!');
        }

        return $vaucher;
    }

    private function canUse(User $user, Vaucher $vaucher): bool
    {
        $vaucherUser = $this->vaucherUserRepository->findByUserVaucher($user, $vaucher);

        return is_null($vaucherUser);
    }

    public function handle(User $user, string $code): array
    {
        $vaucher = $this->getVaucher($code);

        if (!$vaucher->isActive()) {
            throw new Exception('Код уже не действителен');
        }

        if (!$this->canUse($user, $vaucher)) {
            throw new Exception('Вы уже использовали этот код!');
        }

        $type = config('site.vauchers.types.' . $vaucher->getType());
        if (!is_array($type)) {
            throw new Exception('Тип ваучера не найден! Сообщите администрации');
        }

        $data = app()->make($type['handler'])
            ->handle($user, $vaucher, $vaucher->getMessage() ?: $type['message']);

        $data['type'] = $vaucher->getType();

        $vaucher->activate();
        $this->vaucherRepository->update($vaucher);
        $this->vaucherUserRepository->create(new VaucherUser($vaucher, $user));

        event(new UsePromoEvent($user, $vaucher));

        return $data;
    }
}