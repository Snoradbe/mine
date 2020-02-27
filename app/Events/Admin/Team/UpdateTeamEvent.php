<?php


namespace App\Events\Admin\Team;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class UpdateTeamEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var UserAdminGroup
     */
    private $oldUserAdminGroup;

    /**
     * @var UserAdminGroup
     */
    private $newUserAdminGroup;

    /**
     * TransitTeamEvent constructor.
     * @param User $admin
     * @param UserAdminGroup $oldUserAdminGroup
     * @param UserAdminGroup $newUserAdminGroup
     */
    public function __construct(User $admin, UserAdminGroup $oldUserAdminGroup, UserAdminGroup $newUserAdminGroup)
    {
        $this->admin = $admin;
        $this->oldUserAdminGroup = $oldUserAdminGroup;
        $this->newUserAdminGroup = $newUserAdminGroup;
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
    public function getOldUserAdminGroup(): UserAdminGroup
    {
        return $this->oldUserAdminGroup;
    }

    /**
     * @return UserAdminGroup
     */
    public function getNewUserAdminGroup(): UserAdminGroup
    {
        return $this->newUserAdminGroup;
    }

    /**
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->oldUserAdminGroup->getServer();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'target' => [
                'id' => $this->oldUserAdminGroup->getUser()->getId(),
                'name' => $this->oldUserAdminGroup->getUser()->getName()
            ],
            'server' => is_null($this->oldUserAdminGroup->getServer()) ? null : $this->oldUserAdminGroup->getServer()->toArray(),
            'old' => $this->oldUserAdminGroup->getGroup()->toArray(),
            'new' => $this->newUserAdminGroup->getGroup()->toArray()
        ];
    }
}