<?php


namespace App\Events\Api\User;


use App\Entity\Site\User;
use App\Events\Event;

class LevelUpEvent implements Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $oldLevel;

    /**
     * @var int
     */
    private $newLevel;

    /**
     * LevelUpEvent constructor.
     * @param User $user
     * @param int $oldLevel
     * @param int $newLevel
     */
    public function __construct(User $user, int $oldLevel, int $newLevel)
    {
        $this->user = $user;
        $this->oldLevel = $oldLevel;
        $this->newLevel = $newLevel;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getOldLevel(): int
    {
        return $this->oldLevel;
    }

    /**
     * @return int
     */
    public function getNewLevel(): int
    {
        return $this->newLevel;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'old_level' => $this->oldLevel,
            'new_level' => $this->newLevel
        ];
    }
}