<?php


namespace App\Events\Admin\Cabinet;


use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class SetValuteEvent implements Event
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
     * @var int
     */
    private $amount;

    /**
     * SetValuteEvent constructor.
     * @param User $admin
     * @param User $target
     * @param string $type
     * @param int $amount
     */
    public function __construct(User $admin, User $target, string $type, int $amount)
    {
        $this->admin = $admin;
        $this->target = $target;
        $this->type = $type;
        $this->amount = $amount;
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
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
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
            'type' => $this->type,
            'amount' => $this->amount
        ];
    }
}