<?php


namespace App\Events\Admin\Tops;


use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class TopsSettingsEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var array
     */
    private $oldBase;

    /**
     * @var array
     */
    private $newBase;

    /**
     * @var array
     */
    private $oldTops;

    /**
     * @var array
     */
    private $newTops;

    /**
     * TopsSettingsEvent constructor.
     * @param User $admin
     * @param array $oldBase
     * @param array $newBase
     * @param array $oldTops
     * @param array $newTops
     */
    public function __construct(User $admin, array $oldBase, array $newBase, array $oldTops, array $newTops)
    {
        $this->admin = $admin;
        $this->oldBase = $oldBase;
        $this->newBase = $newBase;
        $this->oldTops = $oldTops;
        $this->newTops = $newTops;
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
    public function getOldBase(): array
    {
        return $this->oldBase;
    }

    /**
     * @return array
     */
    public function getNewBase(): array
    {
        return $this->newBase;
    }

    /**
     * @return array
     */
    public function getOldTops(): array
    {
        return $this->oldTops;
    }

    /**
     * @return array
     */
    public function getNewTops(): array
    {
        return $this->newTops;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'old_base' => $this->oldBase,
            'new_base' => $this->newBase,
            'old_tops' => $this->oldTops,
            'new_tops' => $this->newTops
        ];
    }
}