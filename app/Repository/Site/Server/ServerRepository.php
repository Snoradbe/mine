<?php


namespace App\Repository\Site\Server;


use App\Entity\Site\Server;
use Doctrine\ORM\EntityManagerInterface;

interface ServerRepository
{
    public function find(int $id, bool $onlyEnabled = true): ?Server;

    /**
     * @param bool $onlyEnabled
     * @return Server[]
     */
    public function getAll(bool $onlyEnabled = true): array;

    public function create(Server $server, bool $flush = true): EntityManagerInterface;

    public function update(Server $server, bool $flush = true): EntityManagerInterface;

    public function delete(Server $server, bool $flush = true): EntityManagerInterface;
}