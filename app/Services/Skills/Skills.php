<?php


namespace App\Services\Skills;


use App\Entity\Site\User;

class Skills
{
    public const SKIN_SKILL = 1;

    public const CLOAK_SKILL = 2;

    public static function hasHDSkinSkill(User $user): bool
    {
        return $user->hasSkill(null, static::SKIN_SKILL);
    }

    public static function hasCloakSkill(User $user): bool
    {
        return $user->hasSkill(null, static::CLOAK_SKILL);
    }

    public static function hasHDCloakSkill(User $user): bool
    {
        $level = $user->getSkillLevel(null, static::CLOAK_SKILL);

        return $level >= 2;
    }

    private function __construct() {}
    private function __clone() {}

}