<?php


namespace App\Handlers\Admin\Vauchers;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Events\Admin\Vauchers\AddVaucherEvent;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;
use App\Repository\Site\Vaucher\VaucherRepository;
use App\Services\Cabinet\Vaucher\CodeGenerator;
use App\Services\Cabinet\Vaucher\Handlers\Handler;

class AddHandler
{
    private $vaucherRepository;

    private $userRepository;

    private $codeGenerator;

    public function __construct(VaucherRepository $vaucherRepository, UserRepository $userRepository)
    {
        $this->vaucherRepository = $vaucherRepository;
        $this->userRepository = $userRepository;
        $this->codeGenerator = new CodeGenerator($vaucherRepository);
    }

    private function getUser(string $name): ?User
    {
        $user = $this->userRepository->findByName($name);

        return $user;
    }

    private function generateCode(): string
    {
        return $this->codeGenerator->generate();
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
        ?string $code,
        string $type,
        ?string $message,
        $reward,
        int $amount,
        int $count,
        ?string $start,
        ?string $end,
        ?string $for)
    {
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

        $vauchers = [];

        if (!empty(trim($code))) {
            if (!$this->codeGenerator->checkCode($code)) {
                throw new Exception('Такой код уже есть!');
            }

            $vaucher = new Vaucher(
                $code,
                $type,
                $reward,
                $amount,
                $start,
                $end,
                $message,
                $user
            );

            $this->vaucherRepository->create($vaucher);

            $vauchers[] = $vaucher;
        } else {
            for ($i = 0; $i < $count; $i++)
            {
                $code = $this->generateCode();

                $vaucher = new Vaucher(
                    $code,
                    $type,
                    $reward,
                    $amount,
                    $start,
                    $end,
                    $message,
                    $user
                );

                $this->vaucherRepository->create($vaucher);

                $vauchers[] = $vaucher;
            }
        }

        event(new AddVaucherEvent($admin, $vauchers));
    }
}