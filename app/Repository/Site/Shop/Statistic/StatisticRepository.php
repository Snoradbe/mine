<?php


namespace App\Repository\Site\Shop\Statistic;


use App\Entity\Site\Shop\Statistic;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface StatisticRepository
{
    public function find(int $id): ?Statistic;

    public function getAll(int $page): LengthAwarePaginator;

    public function countBuysForTime(?string $date = null): int; //2019-03-19

    public function sumBuysForTime(string $valute, ?string $date = null): int; //2019-03-19

    public function chartForMonth(int $year, int $month): array;

    public function chartForDay(int $year, int $month, int $day): array;

    public function create(Statistic $statistic, bool $flush = true): EntityManagerInterface;

    public function delete(Statistic $statistic, bool $flush = true): EntityManagerInterface;
}