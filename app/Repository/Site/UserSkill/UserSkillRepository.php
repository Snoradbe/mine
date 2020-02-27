<?php


namespace App\Repository\Site\UserSkill;


use App\Entity\Site\UserSkill;

interface UserSkillRepository
{
    public function create(UserSkill $userSkill): void;

    public function update(UserSkill $userSkill): void;
}