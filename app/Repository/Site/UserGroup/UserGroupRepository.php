<?php


namespace App\Repository\Site\UserGroup;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserGroup;
use Doctrine\ORM\EntityManagerInterface;

interface UserGroupRepository
{
    /**
     * @return UserGroup[]
     */
    public function getAll(): array;

    public function find(int $id): ?UserGroup;

    public function findByUser(User $user): array;

    public function findByServer(Server $server): array;

    public function findByUserServer(User $user, Server $server): array;

    /**
     * Истекшие группы
     *
     * @return UserGroup[]
     */
    public function getExpiredGroups(): array;

    /**
     * Группы которые скоро истекут
     *
     * @param int $days
     * @return UserGroup[]
     */
    public function getPreExpiredGroups(int $days = 3): array;

    public function create(UserGroup $userGroup, bool $flush = true): EntityManagerInterface;

    public function update(UserGroup $userGroup, bool $flush = true): EntityManagerInterface;

    public function delete(UserGroup $userGroup, bool $flush = true): EntityManagerInterface;
}