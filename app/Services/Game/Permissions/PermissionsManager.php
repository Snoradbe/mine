<?php


namespace App\Services\Game\Permissions;


interface PermissionsManager
{
    /**
     * Выдача основной группы с удалением других групп.
     * @param array $groups Основные группы которые будут удалены перед добавлением
     * @param string $uuid
     * @param string $group
     */
    public function setPrimaryGroup(array $groups, string $uuid, string $group): void;

    /**
     * @param string $uuid
     * @param string $group
     */
    public function addGroup(string $uuid, string $group): void;

    /**
     * @param string $uuid
     * @param string $group
     */
    public function removeGroup(string $uuid, string $group): void;

    /**
     * @param string $uuid
     * @param string $permission
     */
    public function addPermission(string $uuid, string $permission): void;

    /**
     * @param string $uuid
     * @param string $permission
     */
    public function removePermission(string $uuid, string $permission): void;

    /**
     * 0 - prefix
     * 1 - suffix
     *
     * @var string $uuid
     * @return String[]
     */
    public function getPrefixSuffix(string $uuid): array;

    /**
     * @param string $uuid
     * @param string $prefix
     */
    public function setPrefix(string $uuid, string $prefix): void;

    /**
     * @param string $uuid
     * @param string $suffix
     */
    public function setSuffix(string $uuid, string $suffix): void;

    /**
     * @param string $uuid
     */
    public function removePrefixSuffix(string $uuid): void;

    /**
     * Добавление пермишенов группе
     * @param string $group
     * @param array $permissions
     * @return array
     */
    public function addPermissionsToGroup(string $group, array $permissions): array;

    /**
     * Удаление пермишенов у группы
     * @param string $group
     * @param array $permissions
     */
    public function removePermissionsFromGroup(string $group, array $permissions): void;

    /**
     * Список пермишенов игрока
     * @param string $uuid
     * @return mixed
     */
    public function getPermissions(string $uuid): array;
}