<?php


namespace App\Repository\Site\VoteLog;


use App\Entity\Site\User;
use App\Entity\Site\VoteLog;
use App\Repository\DoctrineConstructor;

class DoctrineVoteLogRepository implements VoteLogRepository
{
    use DoctrineConstructor;

    public function getLatest(): array
    {
        return $this->er->createQueryBuilder('vlog')
            ->orderBy('vlog.id', 'DESC')
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();
    }

    public function getUserToday(User $user): array
    {
        return $this->er->createQueryBuilder('vlog')
            ->where('vlog.user = :user AND vlog.voteDay = :now')
            ->setParameter('user', $user)
            ->setParameter('now', date('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function getCountToday(User $user): int
    {
        return (int) $this->em->createQuery(sprintf(
            'SELECT COUNT(vlog) FROM %s vlog WHERE vlog.voteDay = :now AND vlog.user = :user',
            VoteLog::class
        ))->setParameter('now', date('Y-m-d'))->setParameter('user', $user)->getSingleScalarResult();
    }

    public function getUserLast(User $user): ?VoteLog
    {
        return $this->er->createQueryBuilder('vlog')
            ->where('vlog.user = :user')
            ->setParameter('user', $user)
            ->orderBy('vlog.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function create(VoteLog $voteLog): void
    {
        $this->em->persist($voteLog);
        $this->em->flush();
    }
}