<?php


namespace App\Repository\Site\UserAdminGroup;


use App\Entity\Site\Server;
use App\Entity\Site\UserAdminGroup;
use Doctrine\ORM\EntityManagerInterface;

interface UserAdminGroupRepository
{
    /**
     * @return UserAdminGroup[]
     */
    public function getAll(): array;

    /**
     * @param Server[]|null $servers - если null, значит масс
     * @return UserAdminGroup[]
     */
    public function getAllOnServer(?array $servers): array;

    public function find(int $id): ?UserAdminGroup;

    public function create(UserAdminGroup $group, bool $flush = true): EntityManagerInterface;

    public function delete(UserAdminGroup $group, bool $flush = true): EntityManagerInterface;
}