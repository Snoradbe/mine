<?php


namespace App\Services\Referal\Handlers;


use App\Entity\Site\User;
use App\Repository\Site\User\UserRepository;

class PaymentHandler implements Handler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * PaymentHandler constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     * @param string $type
     * @param array $data - [reward: 40, base_sum: 1500]
     * @return int
     */
    public function handle(User $user, string $type, array $data)
    {
        $referer = $user->getReferer();

        if ($data > 0 && $data <= 100) {
            $sum = $data['base_sum'];
            $percentage = $sum - ceil($sum * ($data['reward'] / 100));

            if ($percentage >= 1) {
                $referer->depositMoney($percentage);
                $this->userRepository->update($referer);
                $user->depositReferalPoints($percentage);
                $this->userRepository->update($user);

                return $percentage;
            }
        }

        return 0;
    }
}