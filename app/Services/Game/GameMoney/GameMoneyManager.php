<?php


namespace App\Services\Game\GameMoney;


use App\Entity\Game\GameMoney\GameMoney;
use App\Entity\Site\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface GameMoneyManager
{
    public function getAll(int $page): LengthAwarePaginator;

    public function getTop(int $count): array;

    public function getMoneyEntity(User $user): GameMoney;

    public function setMoney(User $user, float $amount): void;

    public function update(GameMoney $entity): void;

    public static function getEntityClassname(): string;
}