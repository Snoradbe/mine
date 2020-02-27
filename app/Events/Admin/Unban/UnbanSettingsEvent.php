<?php


namespace App\Events\Admin\Unban;


use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class UnbanSettingsEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var int
     */
    private $price;

    /**
     * UnbanSettingsEvent constructor.
     * @param User $admin
     * @param int $price
     */
    public function __construct(User $admin, int $price)
    {
        $this->admin = $admin;
        $this->price = $price;
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
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'price' => $this->price
        ];
    }
}