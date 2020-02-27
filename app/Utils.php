<?php


namespace App;


use Illuminate\Pagination\LengthAwarePaginator;

final class Utils
{
    private function __construct() {}
    
    public static function paginationData(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'count_pages' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total()
        ];
    }
}