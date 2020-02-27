<?php


namespace App\Repository\Game\LiteBans;


use App\Entity\Game\LiteBans\LiteBansBan;
use App\Entity\Site\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface LiteBansRepository
{
    public function find(int $id): ?LiteBansBan;

    public function findByUser(User $user): ?LiteBansBan;

    public function getAll(int $page, ?string $name = null, ?string $admin = null): LengthAwarePaginator;

    public function create(LiteBansBan $ban): void;

    public function update(LiteBansBan $ban): void;

    public function delete(LiteBansBan $ban): void;
}