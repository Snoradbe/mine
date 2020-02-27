<?php


namespace App\Repository\Game\GameMoney\Fe;


use App\Entity\Game\GameMoney\FeGameMoney;
use App\Entity\Game\GameMoney\GameMoney;
use App\Exceptions\Exception;
use App\Repository\Game\GameMoney\GameMoneyRepository;
use App\Repository\PaginatedDoctrineConstructor;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineFeRepository implements GameMoneyRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    public function findByUser(string $user): GameMoney
    {
        return $this->er->findOneBy(['username' => $user]);
    }

    public function getAll(int $page): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('gm')
            ->orderBy('gm.username', 'ASC')
            ->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function getTop(int $count): array
    {
        return $this->createQueryBuilder('gm')
            ->orderBy('gm.balance', 'DESC')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    public function update(GameMoney $gameMoney): void
    {
        if (!($gameMoney instanceof FeGameMoney)) {
            throw new Exception('GameMoney !instanceof Fe');
        }

        $this->em->flush($gameMoney);
    }
}