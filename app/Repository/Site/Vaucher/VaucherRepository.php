<?php


namespace App\Repository\Site\Vaucher;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use Illuminate\Pagination\LengthAwarePaginator;

interface VaucherRepository
{
    public const PER_PAGE = 30;

    public function getAll(?User $user, bool $onlyActive = true, int $page = 1): LengthAwarePaginator;

    public function find(int $id): ?Vaucher;

    public function findByCode(string $code): ?Vaucher;

    public function create(Vaucher $vaucher): void;

    public function update(Vaucher $vaucher): void;

    public function delete(Vaucher $vaucher): void;
}