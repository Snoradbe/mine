<?php


namespace App\Repository\Site\BugReportMessage;


use App\Entity\Site\BugReport;
use App\Entity\Site\BugReportMessage;
use Illuminate\Pagination\LengthAwarePaginator;

interface BugReportMessageRepository
{
    public const PER_PAGE = 20;

    public function getAll(BugReport $bugReport, int $page): LengthAwarePaginator;

    public function find(int $id): ?BugReportMessage;

    public function create(BugReportMessage $bugReportMessage): void;

    public function delete(BugReportMessage $bugReportMessage): void;
}