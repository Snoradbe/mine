<?php


namespace App\Events\Client\Skills;


use App\Entity\Site\Skill;
use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Event;

class SkillUpEvent extends ClientEvent implements Event
{
    /**
     * @var Skill
     */
    private $skill;

    /**
     * SkillUpEvent constructor.
     * @param User $user
     * @param Skill $skill
     */
    public function __construct(User $user, Skill $skill)
    {
        parent::__construct($user);

        $this->skill = $skill;
    }

    /**
     * @return Skill
     */
    public function getSkill(): Skill
    {
        return $this->skill;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'skill' => $this->skill->toArray()
        ];
    }
}