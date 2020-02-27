<?php


namespace App\Events\Admin\Cabinet;


use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class DeleteSkinCloakEvent implements Event
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
     * @var string
     */
    private $type;

    /**
     * DeleteSkinCloakEvent constructor.
     * @param User $admin
     * @param User $target
     * @param string $type
     */
    public function __construct(User $admin, User $target, string $type)
    {
        $this->admin = $admin;
        $this->target = $target;
        $this->type = $type;
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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
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
            'type' => $this->type
        ];
    }
}