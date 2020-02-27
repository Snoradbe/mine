<?php


namespace App\Events\Admin\Applications\Settings;


use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class EditCooldownEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var int
     */
    private $old;

    /**
     * @var int
     */
    private $new;

    /**
     * EditCooldownEvent constructor.
     * @param User $admin
     * @param int $old
     * @param int $new
     */
    public function __construct(User $admin, int $old, int $new)
    {
        $this->admin = $admin;
        $this->old = $old;
        $this->new = $new;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return int
     */
    public function getOld(): int
    {
        return $this->old;
    }

    /**
     * @return int
     */
    public function getNew(): int
    {
        return $this->new;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'old' => $this->old,
            'new' => $this->new
        ];
    }
}