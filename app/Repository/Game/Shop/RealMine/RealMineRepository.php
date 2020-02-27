<?php


namespace App\Repository\Game\Shop\RealMine;


use App\Entity\Game\Shop\RealMine;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface RealMineRepository
{
    public function find(int $id): ?RealMine;

    public function getAll(Server $server, int $page): LengthAwarePaginator;

    public function getAllByUser(User $user, Server $server, int $page): LengthAwarePaginator;

    public function create(RealMine $realMine, bool $flush = true): EntityManagerInterface;

    public function update(RealMine $realMine, bool $flush = true): EntityManagerInterface;

    public function delete(RealMine $realMine, bool $flush = true): EntityManagerInterface;
}