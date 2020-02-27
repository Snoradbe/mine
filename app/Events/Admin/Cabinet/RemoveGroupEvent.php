<?php


namespace App\Events\Admin\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserGroup;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class RemoveGroupEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var User
     */
    private $target;

    /**
     * @var UserGroup
     */
    private $userGroup;

    /**
     * RemoveGroupEvent constructor.
     * @param User $admin
     * @param User $target
     * @param UserGroup $userGroup
     */
    public function __construct(User $admin, User $target, UserGroup $userGroup)
    {
        $this->admin = $admin;
        $this->target = $target;
        $this->userGroup = $userGroup;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return User
     */
    public function getTarget(): User
    {
        return $this->target;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->userGroup->getServer();
    }

    /**
     * @return UserGroup
     */
    public function getUserGroup(): UserGroup
    {
        return $this->userGroup;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'target' => [
                'id' => $this->target->getId(),
                'name' => $this->target->getName()
            ],
            'user_group' => $this->userGroup->toArray()
        ];
    }
}