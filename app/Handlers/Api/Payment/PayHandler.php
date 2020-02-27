<?php


namespace App\Handlers\Api\Payment;


use App\Entity\Site\User;
use App\Events\Api\User\PaymentEvent;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;
use App\Services\Payment\Payers\Payer;
use App\Services\Payment\Payers\Pool;

class PayHandler
{
    private $userRepository;

    private $pool;

    public function __construct(UserRepository $userRepository, Pool $pool)
    {
        $this->userRepository = $userRepository;
        $this->pool = $pool;
    }

    private function getUser(string $name): User
    {
        $user = $this->userRepository->findByName($name);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    private function getPayer(string $name): Payer
    {
        $payer = $this->pool->find($name);
        if (is_null($payer)) {
            throw new Exception('Такая платежная система не найдена!');
        }

        return $payer;
    }

    public function handle(string $payerName, array $request, string $ip): string
    {
        $payer = $this->getPayer($payerName);

        if (!$payer->validate($request, $ip)) {
			throw new Exception($payer->errorAnswer('Неверная цифровая подпись!'));
		}

        $user = $this->getUser($payer->nickname($request));

        $sum = $payer->sum($request);
        $user->depositMoney($sum);

        $this->userRepository->update($user);

        event(new PaymentEvent($user, $sum, $payer->getName()));

        return $payer->successAnswer();
    }
}