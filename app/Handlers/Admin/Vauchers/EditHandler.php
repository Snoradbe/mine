<?php


namespace App\Handlers\Admin\Vauchers;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Events\Admin\Vauchers\EditVaucherEvent;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;
use App\Repository\Site\Vaucher\VaucherRepository;
use App\Services\Cabinet\Vaucher\Handlers\Handler;

class EditHandler
{
    private $vaucherRepository;

    private $userRepository;

    public function __construct(VaucherRepository $vaucherRepository, UserRepository $userRepository)
    {
        $this->vaucherRepository = $vaucherRepository;
        $this->userRepository = $userRepository;
    }

    private function getVaucher(int $id): Vaucher
    {
        $vaucher = $this->vaucherRepository->find($id);
        if (is_null($vaucher)) {
            throw new Exception('Ваучер не найден!');
        }

        return $vaucher;
    }

    private function getUser(string $name): ?User
    {
        $user = $this->userRepository->findByName($name);

        return $user;
    }

    private function getReward(string $type, string $reward)
    {
        /* @var $handler Handler */
        $handler = app()->make(config('site.vauchers.types.' . $type . '.handler'));

        return $handler->getReward($reward);
    }

    /**
     * @param User $admin - админ
     * @param string|null $code - код
     * @param string $type - тип ваучера
     * @param string|null $message - сообщение при активации
     * @param $reward - награда
     * @param int $amount - количество активаций
     * @param int $count - количество генерируемых ваучеров
     * @param string|null $start - дата начала действия
     * @param string|null $end - дата окончания действия
     * @param string|null $for - кто видит ваучер
     * @throws Exception
     */
    public function handle(
        User $admin,
        int $id,
        string $type,
        ?string $message,
        $reward,
        int $amount,
        ?string $start,
        ?string $end,
        ?string $for): void
    {
        $vaucher = $this->getVaucher($id);
        $old = clone $vaucher;

        $user = null;
        if (!empty($for)) {
            $user = $this->getUser($for);
        }

        $message = empty(trim($message)) ? null : strip_tags($message);
        $reward = $this->getReward($type, $reward);

        try {
            $start = new \DateTimeImmutable($start);
        } catch (\Exception $exception) {
            throw new Exception('Дата начала неправильная!');
        }

        if (!empty(trim($end))) {
            try {
                $end = new \DateTimeImmutable($end);
            } catch (\Exception $exception) {
                throw new Exception('Дата окончания неправильная!');
            }
        } else {
            $end = null;
        }

        $vaucher->setValue($reward);
        $vaucher->setMessage($message);
        $vaucher->setAmount($amount);
        $vaucher->setUser($user);
        $vaucher->setStart($start);
        $vaucher->setEnd($end);

        $this->vaucherRepository->update($vaucher);

        event(new EditVaucherEvent($admin, $old, $vaucher));
    }
}