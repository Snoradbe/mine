<?php


namespace App\Repository\Site\UserSkill;


use App\Entity\Site\UserSkill;
use App\Repository\DoctrineConstructor;

class DoctrineUserSkillRepository implements UserSkillRepository
{
    use DoctrineConstructor;

    public function create(UserSkill $userSkill): void
    {
        $this->em->persist($userSkill);
        $this->em->flush();
    }

    public function update(UserSkill $userSkill): void
    {
        $this->em->flush($userSkill);
    }
}