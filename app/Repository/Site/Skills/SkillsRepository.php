<?php


namespace App\Repository\Site\Skills;


use App\Entity\Site\Server;
use App\Entity\Site\Skill;

interface SkillsRepository
{
    /**
     * @param int $id
     * @return Skill|null
     */
    public function find(int $id): ?Skill;

    /**
     * @param Server|null $server
     * @return Skill[]
     */
    public function getAll(?Server $server = null): array;
}