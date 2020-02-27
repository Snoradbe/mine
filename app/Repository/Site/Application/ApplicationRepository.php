<?php


namespace App\Repository\Site\Application;


use App\Entity\Site\Application;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface ApplicationRepository
{
    public const PER_PAGE = 30;

    /**
     * @param int|null $status
     * @param Server[]|null $servers
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getAll(?int $status, ?array $servers, int $page): LengthAwarePaginator;

    /**
     * @param string $name
     * @param Server[]|null $servers
     * @param int $page
     * @param int|null $status
     * @return LengthAwarePaginator
     */
    public function search(string $name, ?array $servers, int $page, ?int $status = null): LengthAwarePaginator;

    public function find(int $id): ?Application;

    public function findLast(User $user): ?Application;

    public function getCountWait(?array $servers): int;

    public function create(Application $application): void;

    public function update(Application $application): void;

    public function delete(Application $application): void;
}