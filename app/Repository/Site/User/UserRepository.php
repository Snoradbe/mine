<?php


namespace App\Repository\Site\User;


use App\Entity\Site\User;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepository
{
    public function find(int $id): ?User;

    public function findByName(string $name): ?User;

    public function findByUUID(string $uuid): ?User;

    /**
     * @param int $limit
     * @return User[]
     */
    public function getTopVotes(int $limit = 10): array;

    public function clearVotes(): void;

    public function getReferals(User $user, int $page = 1): LengthAwarePaginator;

    public function getHwidBannedUsers(int $page = 1): LengthAwarePaginator;

    public function update(User $user, bool $flush = true): EntityManagerInterface;
}