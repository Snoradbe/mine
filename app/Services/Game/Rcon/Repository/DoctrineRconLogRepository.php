<?php


namespace App\Services\Game\Rcon\Repository;


use App\Entity\Site\Server;
use App\Services\Game\Rcon\Entity\RconLog;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineRconLogRepository implements RconLogRepository
{
    private $em;

    private $er;

    public function __construct(EntityManagerInterface $em, EntityRepository $er)
    {
        $this->em = $em;
        $this->er = $er;
    }

    public function getLastLogs(Server $server): array
    {
        return $this->er
            ->createQueryBuilder('rl')
            ->where('rl.server = :server')
            ->setMaxResults(30)
            ->setParameter('server', $server)
            ->orderBy('rl.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function create(RconLog $log): void
    {
        $this->em->persist($log);
        $this->em->flush();
    }
}