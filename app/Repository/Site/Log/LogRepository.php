<?php


namespace App\Repository\Site\Log;


use App\Entity\Site\Log;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface LogRepository
{
    public const CLIENT_ALL_TYPE = 0;
    public const CLIENT_SPENT_TYPE = 1;
    public const CLIENT_RECEIVED_TYPE = 2;

    public function getAll(?Server $server, ?string $user, ?array $types, int $page): LengthAwarePaginator;

    public function getAllClient(?Server $server, User $user, array $types, int $type, int $page): LengthAwarePaginator;

    public function findByUser(User $user, array $types = [], int $page = 1): LengthAwarePaginator;

    public function create(Log $log): void;
}