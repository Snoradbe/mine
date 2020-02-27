<?php


namespace App\Events\Admin\Cabinet\Settings;


use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class SkinCloakSettingsEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var array
     */
    private $oldSkin;

    /**
     * @var array
     */
    private $newSkin;

    /**
     * @var array
     */
    private $oldCloak;

    /**
     * @var array
     */
    private $newCloak;

    /**
     * SkinCloakSettingsEvent constructor.
     * @param User $admin
     * @param array $oldSkin
     * @param array $newSkin
     * @param array $oldCloak
     * @param array $newCloak
     */
    public function __construct(User $admin, array $oldSkin, array $newSkin, array $oldCloak, array $newCloak)
    {
        $this->admin = $admin;
        $this->oldSkin = $oldSkin;
        $this->newSkin = $newSkin;
        $this->oldCloak = $oldCloak;
        $this->newCloak = $newCloak;
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
    public function getOldSkin(): array
    {
        return $this->oldSkin;
    }

    /**
     * @return array
     */
    public function getNewSkin(): array
    {
        return $this->newSkin;
    }

    /**
     * @return array
     */
    public function getOldCloak(): array
    {
        return $this->oldCloak;
    }

    /**
     * @return array
     */
    public function getNewCloak(): array
    {
        return $this->newCloak;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'old_skin' => $this->oldSkin,
            'new_skin' => $this->newSkin,
            'old_cloak' => $this->oldCloak,
            'new_cloak' => $this->newCloak
        ];
    }
}