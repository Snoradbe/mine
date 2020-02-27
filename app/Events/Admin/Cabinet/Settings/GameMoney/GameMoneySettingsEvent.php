<?php


namespace App\Events\Admin\Cabinet\Settings\GameMoney;


use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class GameMoneySettingsEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var array
     */
    private $oldRates;

    /**
     * @var array
     */
    private $newRates;

    /**
     * @var array
     */
    private $oldManagers;

    /**
     * @var array
     */
    private $newManagers;

    /**
     * GameMoneySettingsEvent constructor.
     * @param User $admin
     * @param array $oldRates
     * @param array $newRates
     * @param array $oldManagers
     * @param array $newManagers
     */
    public function __construct(User $admin, array $oldRates, array $newRates, array $oldManagers, array $newManagers)
    {
        $this->admin = $admin;
        $this->oldRates = $oldRates;
        $this->newRates = $newRates;
        $this->oldManagers = $oldManagers;
        $this->newManagers = $newManagers;
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
    public function getOldRates(): array
    {
        return $this->oldRates;
    }

    /**
     * @return array
     */
    public function getNewRates(): array
    {
        return $this->newRates;
    }

    /**
     * @return array
     */
    public function getOldManagers(): array
    {
        return $this->oldManagers;
    }

    /**
     * @return array
     */
    public function getNewManagers(): array
    {
        return $this->newManagers;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'old_rates' => $this->oldRates,
            'new_rates' => $this->newRates,
            'old_managers' => $this->oldManagers,
            'new_manager' => $this->newManagers
        ];
    }
}