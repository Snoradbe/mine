<?php


namespace App\Services\Shop\Pipelines;


use App\DataObjects\Shop\PipelineObject;
use App\Exceptions\Exception;
use App\Services\Shop\Distributor\Distributor;
use Closure;

class ToStoragePipeline
{
    private $distributors = [];

    public function handle(PipelineObject $po, Closure $next)
    {
        if (!is_null($po->getProduct()->getItem())) {
            $distributor = $this->getDistributor($po->getProduct()->getItem()->getType()->getDistributor());

            $distributor->distribute(
                $po->getUser(),
                $po->getServer(),
                $po->getProduct(),
                $po->getStatistic(),
                $po->getAmount(),
                []
            );
        } else {
            $distributor = $this->getDistributor(config('site.shop.packetDistributor'));
            $distributor->distribute(
                $po->getUser(),
                $po->getServer(),
                $po->getProduct(),
                $po->getStatistic(),
                $po->getAmount(),
                []
            );
        }

        return $next($po);
    }

    private function getDistributor(string $class): Distributor
    {
        if(isset($this->distributors[$class])) {
            return $this->distributors[$class];
        }

        if(!class_exists($class)) {
            throw new Exception("Класс дистрибьютора '$class' не найден!");
        }

        $distributor = app($class);
        $distributors[$class] = $distributor;

        return $distributor;
    }
}