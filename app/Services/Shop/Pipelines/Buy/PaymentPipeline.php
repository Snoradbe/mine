<?php


namespace App\Services\Shop\Pipelines\Buy;


use App\DataObjects\Shop\PipelineObject;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;
use Closure;

class PaymentPipeline
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(PipelineObject $po, Closure $next)
    {
        if($po->getValute() == 'coins') {
            if(!$po->getUser()->hasCoins($po->getResultSum())) {
                throw new Exception("Недостаточно средств на балансе! Необходимая сумма: " . $po->getResultSum());
            }

            $po->getUser()->withdrawCoins($po->getResultSum());
        } else {
            if(!$po->getUser()->hasMoney($po->getResultSum())) {
                throw new Exception("Недостаточно средств на балансе! Необходимая сумма: " . $po->getResultSum());
            }

            $po->getUser()->withdrawMoney($po->getResultSum());
        }

        $this->userRepository->update($po->getUser());

        return $next($po);
    }
}