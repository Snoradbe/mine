<?php


namespace App\Events\Admin\Vauchers;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class AddVaucherEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Vaucher[]
     */
    private $vauchers;

    /**
     * AddVaucherEvent constructor.
     * @param User $admin
     * @param Vaucher[] $vauchers
     */
    public function __construct(User $admin, array $vauchers)
    {
        $this->admin = $admin;
        $this->vauchers = $vauchers;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Vaucher[]
     */
    public function getVauchers(): array
    {
        return $this->vauchers;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'vauchers' => array_map(function (Vaucher $vaucher) {
                return $vaucher->toArray();
            }, $this->vauchers)
        ];
    }
}