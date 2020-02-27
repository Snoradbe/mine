<?php


namespace App\Repository\Site\Group;


use App\Entity\Site\Group;

interface GroupRepository
{
    public function find(int $id): ?Group;

    public function findByName(string $name): ?Group;

    /**
     * @param string $order
     * @param string $sort
     * @return Group[]
     */
    public function getAll(string $order = 'weight', string $sort = 'DESC'): array;

    /**
     * @param bool $onlyPrimary
     * @param string $sort
     * @return Group[]
     */
    public function getAllDonate(bool $onlyPrimary = false, string $sort = 'DESC'): array;

    /**
     * @param bool $onlyPrimary
     * @param string $sort
     * @return Group[]
     */
    public function getAllAdmin(bool $onlyPrimary = false, string $sort = 'DESC'): array;

    public function create(Group $group): void;

    public function update(Group $group): void;

    public function delete(Group $group): void;
}