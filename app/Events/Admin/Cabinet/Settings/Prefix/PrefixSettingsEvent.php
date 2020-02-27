<?php


namespace App\Events\Admin\Cabinet\Settings\Prefix;


use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class PrefixSettingsEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var array
     */
    private $old;

    /**
     * @var array
     */
    private $new;

    /**
     * PrefixSettingsEvent constructor.
     * @param User $admin
     * @param array $old
     * @param array $new
     */
    public function __construct(User $admin, array $old, array $new)
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
     * @return array
     */
    public function getOld(): array
    {
        return $this->old;
    }

    /**
     * @return array
     */
    public function getNew(): array
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