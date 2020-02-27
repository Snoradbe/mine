<?php


namespace App\Events\Admin\Team;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class DeleteFromTeamEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var UserAdminGroup
     */
    private $userAdminGroup;

    /**
     * DeleteFromTeamEvent constructor.
     * @param User $admin
     * @param UserAdminGroup $userAdminGroup
     */
    public function __construct(User $admin, UserAdminGroup $userAdminGroup)
    {
        $this->admin = $admin;
        $this->userAdminGroup = $userAdminGroup;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return UserAdminGroup
     */
    public function getUserAdminGroup(): UserAdminGroup
    {
        return $this->userAdminGroup;
    }

    /**
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->userAdminGroup->getServer();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'target' => [
                'id' => $this->userAdminGroup->getUser()->getId(),
                'name' => $this->userAdminGroup->getUser()->getName()
            ],
            'group' => $this->userAdminGroup->getGroup()->toArray(),
            'joined' => $this->userAdminGroup->getCreatedAt()->getTimestamp()
        ];
    }
}