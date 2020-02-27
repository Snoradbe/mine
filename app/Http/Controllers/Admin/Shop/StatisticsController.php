<?php


namespace App\Http\Controllers\Admin\Shop;


use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Repository\Site\Shop\Statistic\StatisticRepository;

class StatisticsController extends Controller
{
    public function render(StatisticRepository $statisticRepository, ProductRepository $productRepository)
    {
        NavMenu::$active = 'shop.statistics';

        $today = date('Y-m-d');

        //dd($statisticRepository->chartForDay((int)date('Y'), (int)date('m'), (int)date('d')));

        $currentDay = (int) date('d');
        $currentMonth = (int) date('m');
        $currentYear = (int) date('Y');
        $chartBuysMonth = [];
        for($i = 1; $i <= $currentDay; $i++)
        {
            if ($i < 10) {
                $i = '0' . $i;
            }
            $month = $currentMonth < 10 ? '0' . $currentMonth : $currentMonth;
            $chartBuysMonth["$currentYear-$month-$i"] = 0;
        }
        foreach ($statisticRepository->chartForMonth($currentYear, $currentMonth) as $chart)
        {
            $chartBuysMonth[$chart['day']] = (int)$chart['total'];
        }

        $chartBuysToday = array_fill(1, 24, 0);
        foreach ($statisticRepository->chartForDay($currentYear, $currentMonth, $currentDay) as $chart)
        {
            $chartBuysToday[(int)$chart['hour']] = (int)$chart['total'];
        }

        //dd($chartBuysMonth);

        return view('admin.shop.statistics', [
            'countBuysAll' => $statisticRepository->countBuysForTime(),
            'countBuysToday' => $statisticRepository->countBuysForTime($today),
            'sumBuysAll' => $statisticRepository->sumBuysForTime('rub'),
            'sumBuysToday' => $statisticRepository->sumBuysForTime('rub', $today),

            'topBuys' => $productRepository->getTopBuysProducts(),
            'chartBuysMonth' => $chartBuysMonth,
            'chartBuysToday' => $chartBuysToday,
        ]);
    }
}