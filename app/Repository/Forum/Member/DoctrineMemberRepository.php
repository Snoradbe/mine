<?php


namespace App\Repository\Forum\Member;


use App\Entity\Forum\Member;
use App\Repository\DoctrineConstructor;

class DoctrineMemberRepository implements MemberRepository
{
    use DoctrineConstructor;

    public function find(string $name): ?Member
    {
        return $this->er->createQueryBuilder('fmember')
            ->where('fmember.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function update(Member $member): void
    {
        $this->em->flush($member);
    }
}