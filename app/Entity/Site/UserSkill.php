<?php


namespace App\Entity\Site;


use Doctrine\ORM\Mapping as ORM;

/**
 * UserSkill
 *
 * @ORM\Table(name="pr_user_skills")
 * @ORM\Entity
 */
class UserSkill
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="smallint", nullable=false)
     */
    private $level = 1;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    private $user;

    /**
     * @var Skill
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Skill")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="skill_id", referencedColumnName="id")
     * })
     */
    private $skill;

    /**
     * UserSkill constructor.
     *
     * @param Skill $skill
     * @param User $user
     * @param int $level
     */
    public function __construct(User $user, Skill $skill, int $level)
    {
        $this->user = $user;
        $this->skill = $skill;
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return void
     */
    public function addLevel(): void
    {
        $this->level++;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
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
            'id' => $this->id,
            'skill' => $this->getSkill()->toArray(),
            'level' => $this->getLevel()
        ];
    }
}