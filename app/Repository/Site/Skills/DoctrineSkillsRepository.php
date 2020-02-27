<?php


namespace App\Repository\Site\Skills;


use App\Entity\Site\Server;
use App\Entity\Site\Skill;
use App\Repository\DoctrineConstructor;

class DoctrineSkillsRepository implements SkillsRepository
{
    use DoctrineConstructor;

    public function find(int $id): ?Skill
    {
        return $this->er->find($id);
    }

    public function getAll(?Server $server = null): array
    {
        $query = $this->er->createQueryBuilder('skill');
        if (!is_null($server)) {
            $query->where('skill.server = :server')
                ->setParameter('server', $server);
        }
        
        return $query->getQuery()->getResult();
    }
}