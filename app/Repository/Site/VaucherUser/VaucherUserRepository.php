<?php


namespace App\Repository\Site\VaucherUser;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Entity\Site\VaucherUser;
use Illuminate\Pagination\LengthAwarePaginator;

interface VaucherUserRepository
{
    public const PER_PAGE = 60;

    public function getAll(int $page = 1): LengthAwarePaginator;

    public function getByUser(User $user, int $page = 1): LengthAwarePaginator;

    public function getByVaucher(Vaucher $vaucher, int $page = 1): LengthAwarePaginator;

    public function find(int $id): ?VaucherUser;

    public function findByUserVaucher(User $user, Vaucher $vaucher): ?VaucherUser;

    public function create(VaucherUser $vaucherUser): void;

    public function delete(VaucherUser $vaucherUser): void;
}