<?php


namespace App\Repository\Site\Server;


use App\Entity\Site\Server;
use App\Repository\DoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineServerRepository implements ServerRepository
{
    use DoctrineConstructor;

    public function find(int $id, bool $onlyEnabled = true): ?Server
    {
        if ($onlyEnabled) {
            return $this->er->createQueryBuilder('server')
                ->where('server.id = :id AND server.enabled = 1')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $this->er->find($id);
    }

    public function getAll(bool $onlyEnabled = true): array
    {
        if ($onlyEnabled) {
            return $this->er->createQueryBuilder('server')
                ->where('server.enabled = 1')
                ->getQuery()
                ->getResult();
        }

        return $this->er->findAll();
    }

    public function create(Server $server, bool $flush = true): EntityManagerInterface
    {
        $this->em->persist($server);

        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }

    public function update(Server $server, bool $flush = true): EntityManagerInterface
    {
        if ($flush) {
            $this->em->flush($server);
        }

        return $this->em;
    }

    public function delete(Server $server, bool $flush = true): EntityManagerInterface
    {
        $this->em->remove($server);

        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }
}