<?php


namespace App\Repository\Game\PermissionsEx;


interface PermissionInheritanceRepository
{
    public function setPrimaryGroup(array $groups, string $uuid, string $group): void;

    public function addGroup(string $uuid, string $group): void;

    public function removeGroup(string $uuid, string $group): void;
}