<?php


namespace App\Repository\Game\ScreenShoter;


use Illuminate\Pagination\LengthAwarePaginator;

interface ScreenShoterRepository
{
    public const PER_PAGE = 32;

    public function getAll(int $page): LengthAwarePaginator;

    public function getCountPerDays(string $minDate, string $maxDate, ?string $name = null): array;

    public function getForDate(\DateTime $date, int $page, ?string $name = null): LengthAwarePaginator;
}