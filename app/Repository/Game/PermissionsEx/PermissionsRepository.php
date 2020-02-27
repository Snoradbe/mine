<?php


namespace App\Repository\Game\PermissionsEx;


use App\Entity\Game\PermissionsEx\Permission;

interface PermissionsRepository
{
    /**
     * @param string $group
     * @return Permission[]
     */
    public function getPermissionsByGroup(string $group): array;

    /**
     * @param string $uuid
     * @return Permission[]
     */
    public function getPermissionsByUser(string $uuid): array;

    public function deletePermissionsByGroup(string $group, array $permissions): void;

    /**
     * @var string $uuid
     * @return Permission[]
     */
    public function findPrefixSuffix(string $uuid): array;

    //public function findByUserAndPermission(string $uuid, string $permission);

    public function create(Permission $permission): void;

    public function remove(Permission $permission): void;

    public function removeByName(string $uuid, string $permission): void;
}