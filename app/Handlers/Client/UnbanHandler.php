<?php


namespace App\Handlers\Client;


use App\Entity\Site\User;
use App\Events\Client\Unban\UnbanEvent;
use App\Exceptions\Exception;
use App\Repository\Game\LiteBans\LiteBansRepository;
use App\Repository\Site\User\UserRepository;
use App\Services\Discounts\Discounts;
use App\Services\Settings\DataType;

class UnbanHandler
{
    private $userRepository;

    private $liteBansRepository;

    public function __construct(UserRepository $userRepository, LiteBansRepository $liteBansRepository)
    {
        $this->userRepository = $userRepository;
        $this->liteBansRepository = $liteBansRepository;
    }

    public function handle(User $user): void
    {
        $price = settings('unban', DataType::INT, 9999);

        $ban = $this->liteBansRepository->findByUser($user);
        if (is_null($ban)) {
            throw new Exception('Вы не забанены! Возможно срок бана истек');
        }

        $discount = Discounts::getInstance()->getDiscount(null, 'unban', null, null);
        $price = Discounts::getPriceWithDiscount($price, $discount);

        $user->withdrawMoney($price);
        $this->userRepository->update($user);

        $ban->unban(null);
        $this->liteBansRepository->update($ban);

        event(new UnbanEvent($user, $price, $ban->toArray()));
    }
}