<?php


namespace App\Services\Game\Permissions\PermissionsEx;


use App\Entity\Game\PermissionsEx\Permission;
use App\Services\Game\Permissions\PermissionsManager;
use Doctrine\Common\Collections\ArrayCollection;

class PermissionsExManager implements PermissionsManager
{
    /**
     * @var \App\Repository\Game\PermissionsEx\PermissionsRepository
     */
    private $permissionsRepository;

    /**
     * @var \App\Repository\Game\PermissionsEx\DoctrinePermissionInheritanceRepository
     */
    private $permissionsInheritanceRepository;

    public function __construct(array $config, string $connection)
    {
        /* @var \App\Repository\Game\PermissionsEx\DoctrinePermissionsRepository $repository */
        $this->permissionsRepository = doctrine_connection($config['repository_perms'], \App\Entity\Game\PermissionsEx\Permission::class, $connection);

        /* @var \App\Repository\Game\PermissionsEx\DoctrinePermissionInheritanceRepository $repository */
        $this->permissionsInheritanceRepository = doctrine_connection($config['repository_groups'], \App\Entity\Game\PermissionsEx\PermissionInheritance::class, $connection);
    }

    public function setPrimaryGroup(array $groups, string $uuid, string $group): void
    {
        $this->permissionsInheritanceRepository->setPrimaryGroup($groups, $uuid, $group);
    }

    public function addGroup(string $uuid, string $group): void
    {
        $this->permissionsInheritanceRepository->addGroup($uuid, $group);
    }

    public function removeGroup(string $uuid, string $group): void
    {
        $this->permissionsInheritanceRepository->removeGroup($uuid, $group);
    }

    public function addPermission(string $uuid, string $permission): void
    {
        $this->permissionsRepository->create(new Permission(
            $uuid,
            1,
            $permission
        ));
    }

    public function removePermission(string $uuid, string $permission): void
    {
        $this->permissionsRepository->removeByName($uuid, $permission);
    }

    public function getPrefixSuffix(string $uuid): array
    {
        /* @var \App\Entity\Game\PermissionsEx\Permission[] $permissions */
        $permissions = $this->permissionsRepository->findPrefixSuffix($uuid);
        if (count($permissions) != 2) {
            return ['', ''];
        }

        $prefix = $suffix = '';

        foreach ($permissions as $perm)
        {
            if ($perm->getPermission() == 'prefix') {
                $prefix = $perm->getValue();
            } elseif ($perm->getPermission() == 'suffix') {
                $suffix = $perm->getValue();
            }
        }

        return [$prefix, $suffix];
    }

    public function setPrefix(string $uuid, string $prefix): void
    {
        $this->permissionsRepository->removeByName($uuid, 'prefix');
        $this->permissionsRepository->create(new Permission(
            $uuid,
            1,
            'prefix',
            null,
            $prefix
        ));
    }

    public function setSuffix(string $uuid, string $suffix): void
    {
        $this->permissionsRepository->removeByName($uuid, 'suffix');
        $this->permissionsRepository->create(new Permission(
            $uuid,
            1,
            'suffix',
            null,
            $suffix
        ));
    }

    public function removePrefixSuffix(string $uuid): void
    {
        $this->permissionsRepository->removeByName($uuid, 'prefix');
        $this->permissionsRepository->removeByName($uuid, 'suffix');
    }

    public function addPermissionsToGroup(string $group, array $permissions): array
    {
        $serverPermissions = new ArrayCollection($this->permissionsRepository->getPermissionsByGroup($group));
        $exists = $serverPermissions->filter(function (Permission $permission) use ($permissions) {
            return in_array($permission->getPermission(), $permissions);
        });

        $result = [];

        foreach ($permissions as $permission)
        {
            if ($exists->filter(function (Permission $perm) use ($permission) {return $perm->getPermission() == $permission;})->first()) {
                continue;
            }

            $this->permissionsRepository->create(new Permission(
                $group,
                0,
                $permission
            ));

            $result[] = $permission;
        }

        return $result;
    }

    public function removePermissionsFromGroup(string $group, array $permissions): void
    {
        $this->permissionsRepository->deletePermissionsByGroup($group, $permissions);
    }

    public function getPermissions(string $uuid): array
    {
        $permissions = [];
        foreach ($this->permissionsRepository->getPermissionsByUser($uuid) as $permission)
        {
            $permissions[] = $permission->getPermission();
        }

        return $permissions;
    }
}