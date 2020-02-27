<?php


namespace App\Events\Admin\Vauchers;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class EditVaucherEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Vaucher
     */
    private $oldVaucher;

    /**
     * @var Vaucher
     */
    private $newVaucher;

    /**
     * EditVaucherEvent constructor.
     * @param User $admin
     * @param Vaucher $oldVaucher
     * @param Vaucher $newVaucher
     */
    public function __construct(User $admin, Vaucher $oldVaucher, Vaucher $newVaucher)
    {
        $this->admin = $admin;
        $this->oldVaucher = $oldVaucher;
        $this->newVaucher = $newVaucher;
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
    public function getOldVaucher(): Vaucher
    {
        return $this->oldVaucher;
    }

    /**
     * @return Vaucher
     */
    public function getNewVaucher(): Vaucher
    {
        return $this->newVaucher;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'old' => $this->oldVaucher->toArray(),
            'new' => $this->newVaucher->toArray()
        ];
    }
}