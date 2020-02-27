<?php


namespace App\Events\Admin\Banlist;


use App\Entity\Game\LiteBans\LiteBansBan;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class BanEvent implements Event
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
     * @var LiteBansBan
     */
    private $ban;

    /**
     * BanEvent constructor.
     * @param User $admin
     * @param User $target
     * @param LiteBansBan $ban
     */
    public function __construct(User $admin, User $target, LiteBansBan $ban)
    {
        $this->admin = $admin;
        $this->target = $target;
        $this->ban = $ban;
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
     * @return LiteBansBan
     */
    public function getBan(): LiteBansBan
    {
        return $this->ban;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'target' => [
                'id' => $this->target->getId(),
                'name' => $this->target->getName(),
            ],
            'ban' => [
                'reason' => $this->ban->getReason(),
                'until' => $this->ban->getUntil(true)
            ]
        ];
    }
}