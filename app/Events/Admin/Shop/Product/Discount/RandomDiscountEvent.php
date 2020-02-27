<?php


namespace App\Events\Admin\Shop\Product\Discount;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class RandomDiscountEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Server|null
     */
    private $server;

    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;

    /**
     * @var int
     */
    private $days;

    /**
     * @var string
     */
    private $type;

    /**
     * RandomDiscountEvent constructor.
     * @param User $admin
     * @param Server|null $server
     * @param int $min
     * @param int $max
     * @param int $days
     * @param string $type
     */
    public function __construct(User $admin, ?Server $server, int $min, int $max, int $days, string $type)
    {
        $this->admin = $admin;
        $this->server = $server;
        $this->min = $min;
        $this->max = $max;
        $this->days = $days;
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
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @return int
     */
    public function getDays(): int
    {
        return $this->days;
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
            'min' => $this->min,
            'max' => $this->max,
            'days' => $this->days,
            'type' => $this->type
        ];
    }
}