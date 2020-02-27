<?php


namespace App\Services\Shop\Pipelines\Buy;


use App\DataObjects\Shop\PipelineObject;
use App\Entity\Site\Shop\Statistic;
use App\Repository\Site\Shop\Statistic\StatisticRepository;
use App\Services\Permissions\Permissions;
use Closure;

class ToStatisticsPipeline
{
    private $statisticRepository;

    public function __construct(StatisticRepository $statisticRepository)
    {
        $this->statisticRepository = $statisticRepository;
    }

    public function handle(PipelineObject $po, Closure $next)
    {
        //Если есть права на упревление магазином или игнорирование статистики, то не нужно в статистику записывать покупки
        if (
            $po->getUser()->permissions()->hasPermission(Permissions::SHOP_NO_STATISTIC)
            ||
            $po->getUser()->permissions()->hasPermission(Permissions::MP_SHOP_MANAGE)
        ) {
            //return $next($po);
        }

        $statistic = new Statistic(
            $po->getUser(),
            $po->getProduct(),
            $po->getAmount(),
            $po->getResultSum(),
            $po->getValute()
        );

        $this->statisticRepository->create($statistic);

        $po->setStatistic($statistic);

        return $next($po);
    }
}