<?php


namespace App\Repository\Game\PermissionsEx;


use App\Entity\Game\PermissionsEx\PermissionInheritance;
use App\Repository\DoctrineConstructor;

class DoctrinePermissionInheritanceRepository implements PermissionInheritanceRepository
{
    use DoctrineConstructor;

    public function setPrimaryGroup(array $groups, string $uuid, string $group): void
    {
        $this->em->createQuery(sprintf(
        /* @lang */
            'DELETE FROM %s pi WHERE pi.child = :uuid AND pi.parent IN (:groups)',
            PermissionInheritance::class
        ))->setParameter('uuid', $uuid)->setParameter('groups', $groups)->execute();

        $perm = new PermissionInheritance($uuid, $group, 1);

        $this->em->persist($perm);
        $this->em->flush();
    }

    public function addGroup(string $uuid, string $group): void
    {
        $perm = new PermissionInheritance($uuid, $group, 1);

        $this->em->persist($perm);
        $this->em->flush();
    }

    public function removeGroup(string $uuid, string $group): void
    {
        $this->em->createQuery(sprintf(
        /* @lang */
            'DELETE FROM %s pi WHERE pi.child = :uuid AND pi.parent = :group',
            PermissionInheritance::class
        ))->setParameter('uuid', $uuid)->setParameter('group', $group)->execute();
    }
}