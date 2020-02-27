<?php


namespace App\Repository\Site\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface BugReportRepository
{
    public const PER_PAGE = 10;

    public function getAll(int $page): LengthAwarePaginator;

    public function getAllUser(User $user, int $page): LengthAwarePaginator;

    public function find(int $id): ?BugReport;

    public function getCountToday(User $user): int;

    public function getCountWait(?array $servers): int;

    public function create(BugReport $bugReport): void;

    public function update(BugReport $bugReport): void;

    public function delete(BugReport $bugReport): void;
}