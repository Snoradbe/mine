<?php


namespace App\Services\Game\GameMoney\Fe;


use App\Entity\Game\GameMoney\FeGameMoney;
use App\Entity\Game\GameMoney\GameMoney;
use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Repository\Game\GameMoney\GameMoneyRepository;
use App\Services\Game\GameMoney\GameMoneyManager;
use Illuminate\Pagination\LengthAwarePaginator;

class FeGameMoneyManager implements GameMoneyManager
{
    private $repository;

    public function __construct(GameMoneyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(int $page): LengthAwarePaginator
    {
        return $this->repository->getAll($page);
    }

    public function getTop(int $count): array
    {
        return $this->repository->getTop($count);
    }

    public function getMoneyEntity(User $user): GameMoney
    {
        return $this->repository->findByUser($user->getName());
    }

    public function setMoney(User $user, float $amount): void
    {
        $entity = $this->getMoneyEntity($user);
        if (is_null($entity)) {
            throw new Exception('Entity Fe Game Money not found!');
        }

        $entity->setMoney($amount);
        $this->repository->update($entity);
    }

    public function update(GameMoney $entity): void
    {
        $this->repository->update($entity);
    }

    public static function getEntityClassname(): string
    {
        return FeGameMoney::class;
    }
}