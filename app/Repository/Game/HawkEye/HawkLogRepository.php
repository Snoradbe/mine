<?php


namespace App\Repository\Game\HawkEye;


use App\Services\Game\HawkLogs\HawkSearch;
use Illuminate\Pagination\LengthAwarePaginator;

interface HawkLogRepository
{
    public function getAll(HawkSearch $search, int $page): LengthAwarePaginator;
}