<?php


namespace App\Handlers\Client\Skills;


use App\Entity\Site\Skill;
use App\Entity\Site\User;
use App\Entity\Site\UserSkill;
use App\Events\Client\Skills\SkillUpEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Skills\SkillsRepository;
use App\Repository\Site\User\UserRepository;
use App\Repository\Site\UserSkill\UserSkillRepository;

class SkillUpHandler
{
    private $skillRepository;

    private $userSkillRepository;

    private $userRepository;

    public function __construct(SkillsRepository $skillsRepository, UserSkillRepository $userSkillRepository, UserRepository $userRepository)
    {
        $this->skillRepository = $skillsRepository;
        $this->userSkillRepository = $userSkillRepository;
        $this->userRepository = $userRepository;
    }

    private function getSkill(int $id): Skill
    {
        $skill = $this->skillRepository->find($id);
        if (is_null($skill)) {
            throw new Exception('Улучшение не найдено!');
        }

        return $skill;
    }

    public function handle(User $user, int $skillId): UserSkill
    {
        if ($user->getSkillPoints() < 1) {
            throw new Exception('У вас недостаточно очков для улучшения!');
        }

        $skill = $this->getSkill($skillId);

        $userSkill = $user->getSkills()->filter(function (UserSkill $userSkill) use ($skill) {
            return $userSkill->getSkill() === $skill;
        })->first();

        $user->withdrawSkillPoints(1, false);
        $this->userRepository->update($user);

        if ($userSkill instanceof UserSkill) {
            if ($userSkill->getLevel() >= $skill->getMaxLevel()) {
                throw new Exception('Достигнут максимальный уровень улучшения!');
            }

            $userSkill->addLevel();
            $this->userSkillRepository->update($userSkill);
        } else {
            $userSkill = new UserSkill($user, $skill, 1);
            $this->userSkillRepository->create($userSkill);
        }

        event(new SkillUpEvent($user, $skill));

        return $userSkill;
    }
}