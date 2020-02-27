<?php


namespace App\Repository\Game\LiteBans;


use App\Entity\Game\LiteBans\LiteBansBan;
use App\Entity\Site\User;
use App\Repository\PaginatedDoctrineConstructor;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineLiteBansRepository implements LiteBansRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    private const PER_PAGE = 30;

    public function find(int $id): ?LiteBansBan
    {
        return $this->er->find($id);
    }

    public function findByUser(User $user): ?LiteBansBan
    {
        return $this->er->findOneBy(['uuid' => $user->getUuid(), 'active' => true]);
    }

    public function getAll(int $page, ?string $name = null, ?string $admin = null): LengthAwarePaginator
    {
        $query = $this->createQueryBuilder('lbb')
            ->select('lbb', 'lbh')
            ->join('lbb.name', 'lbh')
            ->where('lbb.active = 1 AND (lbb.until > :now OR lbb.until < 1)')
            ->orderBy('lbb.id', 'DESC')
            ->setParameter('now', time() * 1000);

        if (!empty($name)) {
            $query->andWhere('lbh.name LIKE :name')
                ->setParameter('name', "$name%");
        }

        if (!empty($admin)) {
            $query->andWhere('lbb.bannedByName LIKE :admin')
                ->setParameter('admin', "$admin%");
        }

        return $this->paginate(
            $query->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function create(LiteBansBan $ban): void
    {
        $this->em->getConnection()->executeUpdate(
            'INSERT INTO litebans_bans SET uuid = ?, reason = ?, banned_by_uuid = ?, banned_by_name = ?, time = ?, until = ?, active = b\'1\', silent = 0, ipban = 0',
            [$ban->getUuid(), $ban->getReason(), $ban->getAdminUuid(), $ban->getAdmin(), time() * 1000, $ban->getUntil()]
        );
    }

    public function update(LiteBansBan $ban): void
    {
        $this->em->flush($ban);
    }

    public function delete(LiteBansBan $ban): void
    {
        $this->em->remove($ban);
        $this->em->flush();
    }
}