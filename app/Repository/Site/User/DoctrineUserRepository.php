<?php


namespace App\Repository\Site\User;


use App\Entity\Site\User;
use App\Repository\DoctrineConstructor;
use App\Repository\PaginatedDoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineUserRepository implements UserRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    public function find(int $id): ?User
    {
        return $this->er->find($id);
    }

    public function findByName(string $name): ?User
    {
        return $this->er->findOneBy(['name' => $name]);
    }

    public function findByUUID(string $uuid): ?User
    {
        return $this->er->findOneBy(['uuid' => $uuid]);
    }

    public function getTopVotes(int $limit = 10): array
    {
        $sql = 'SELECT u.name, u.votes, v.created_at, v.top FROM dle_users u left join pr_vote_logs v on v.id = (SELECT v.id FROM pr_vote_logs v WHERE v.user_id = u.user_id ORDER BY v.id desc limit 1) WHERE u.user_id > 0 ORDER BY u.votes desc LIMIT ' . $limit;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('name', 'name');
        $rsm->addScalarResult('votes', 'votes');
        $rsm->addScalarResult('created_at', 'created_at');
        $rsm->addScalarResult('top', 'top');

        return $this->em->createNativeQuery($sql, $rsm)->getResult();

        /*return $this->er->createQueryBuilder('us')
            ->orderBy('us.votes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();*/
    }

    public function clearVotes(): void
    {
        $this->er->createQueryBuilder('u')
            ->update()
            ->set('u.votes', '0')
            ->getQuery()
            ->execute();
    }

    public function getReferals(User $user, int $page = 1): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('u')
                ->where('u.referer = :user')
                ->setParameter('user', $user)
                ->orderBy('u.id', 'DESC')
                ->getQuery(),
            15,
            $page,
            false
        );
    }

    public function getHwidBannedUsers(int $page = 1): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('u')
                ->join('u.hwid', 'hwid')
                ->where('u.hwid IS NOT NULL AND hwid.banned = 1')
                ->orderBy('u.name', 'ASC')
                ->getQuery(),
            30,
            $page,
            false
        );
    }

    public function update(User $user, bool $flush = true): EntityManagerInterface
    {
        if ($flush) {
            $this->em->flush($user);
        }

        return $this->em;
    }
}