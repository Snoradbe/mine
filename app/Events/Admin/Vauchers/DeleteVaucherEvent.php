<?php


namespace App\Events\Admin\Vauchers;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class DeleteVaucherEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Vaucher
     */
    private $vaucher;

    /**
     * DeleteVaucherEvent constructor.
     * @param User $admin
     * @param Vaucher $vaucher
     */
    public function __construct(User $admin, Vaucher $vaucher)
    {
        $this->admin = $admin;
        $this->vaucher = $vaucher;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Vaucher
     */
    public function getVaucher(): Vaucher
    {
        return $this->vaucher;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'vaucher' => $this->vaucher->toArray()
        ];
    }
}