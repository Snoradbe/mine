<?php


namespace App\Events\Admin\Applications\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use Illuminate\Contracts\Support\Arrayable;

class DeleteGroupEvent implements Arrayable
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Group
     */
    private $group;

    /**
     * @var array
     */
    private $settings;

    /**
     * DeleteGroupEvent constructor.
     * @param User $admin
     * @param Group $group
     * @param array $settings
     */
    public function __construct(User $admin, Group $group, array $settings)
    {
        $this->admin = $admin;
        $this->group = $group;
        $this->settings = $settings;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'group' => $this->group->toArray(),
            'settings' => $this->settings
        ];
    }
}