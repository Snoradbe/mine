<?php


namespace App\Services\Referal\Handlers;


use App\Entity\Site\ReferalLog;
use App\Entity\Site\User;
use App\Repository\Site\User\UserRepository;

class LevelUpHandler implements Handler
{
    private $userRepository;

    /**
     * LevelUpHandler constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Если тут добавлять тип награды, то нужно также добавить ее в конфиг site.referal.handlers
     *
     * @param User $user
     * @param string $type - level_2
     * @param array $data - [reward: ['money': 10, 'coins': 20]]
     * @return void
     */
    public function handle(User $user, string $type, array $data)
    {
        $referer = $user->getReferer();

        $reward = $data['reward'];

        $em = null;

        if (isset($reward['money'])) {
            $referer->depositMoney($reward['money']);
            $em = $this->userRepository->update($referer, false);
        }

        if (isset($reward['coins'])) {
            $referer->depositCoins($reward['coins']);
            $em = $this->userRepository->update($referer, false);
        }

        if (!is_null($em)) {
            $user->getReferalSteps()->add(new ReferalLog($user, $type));

            $em->flush();
        }
    }
}