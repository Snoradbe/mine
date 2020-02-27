<?php


namespace App\Repository\Game\GameMoney;


use App\Entity\Game\GameMoney\GameMoney;
use Illuminate\Pagination\LengthAwarePaginator;

interface GameMoneyRepository
{
    public const PER_PAGE = 30;
    
    public function findByUser(string $user): GameMoney;

    public function getAll(int $page): LengthAwarePaginator;

    public function getTop(int $count): array;

    public function update(GameMoney $gameMoney): void;
}