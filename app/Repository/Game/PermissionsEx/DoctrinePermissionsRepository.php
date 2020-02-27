<?php


namespace App\Repository\Game\PermissionsEx;


use App\Entity\Game\PermissionsEx\Permission;
use App\Repository\DoctrineConstructor;

class DoctrinePermissionsRepository implements PermissionsRepository
{
    use DoctrineConstructor;

    public function getPermissionsByGroup(string $group): array
    {
        return $this->er->createQueryBuilder('perm')
            ->where('perm.name = :group AND perm.type = 0')
            ->setParameter('group', $group)
            ->orderBy('perm.permission', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getPermissionsByUser(string $uuid): array
    {
        return $this->er->createQueryBuilder('perm')
            ->where('perm.name = :name AND perm.type = 1')
            ->setParameter('name', $uuid)
            ->orderBy('perm.permission', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function deletePermissionsByGroup(string $group, array $permissions): void
    {
        $this->er->createQueryBuilder('perm')
            ->delete()
            ->where('perm.name = :group AND perm.type = 0 AND perm.permission IN (:perms)')
            ->setParameter('group', $group)
            ->setParameter('perms', $permissions)
            ->getQuery()
            ->execute();
    }

    public function findPrefixSuffix(string $uuid): array
    {
        return $this->er->createQueryBuilder('perm')
            ->where('perm.name = :uuid AND perm.permission IN (\'prefix\', \'suffix\')')
            ->setParameter('uuid', $uuid)
            ->groupBy('perm.permission')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();
    }

    public function create(Permission $permission): void
    {
        $this->em->persist($permission);
        $this->em->flush();
    }

    public function remove(Permission $permission): void
    {
        $this->em->remove($permission);
        $this->em->flush();
    }

    public function removeByName(string $uuid, string $permission): void
    {
        $this->er->createQueryBuilder('perm')
            ->delete(Permission::class, 'perm')
            ->where('perm.name = :uuid AND perm.permission = :perm')
            ->setParameter('uuid', $uuid)
            ->setParameter('perm', $permission)
            ->getQuery()
            ->execute();
    }
}