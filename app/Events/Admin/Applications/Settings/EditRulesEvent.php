<?php


namespace App\Events\Admin\Applications\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use Illuminate\Contracts\Support\Arrayable;

class EditRulesEvent implements Arrayable
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
    private $oldRules;

    /**
     * @var array
     */
    private $newRules;

    /**
     * EditRulesEvent constructor.
     * @param User $admin
     * @param Group $group
     * @param array $oldRules
     * @param array $newRules
     */
    public function __construct(User $admin, Group $group, array $oldRules, array $newRules)
    {
        $this->admin = $admin;
        $this->group = $group;
        $this->oldRules = $oldRules;
        $this->newRules = $newRules;
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
    public function getOldRules(): array
    {
        return $this->oldRules;
    }

    /**
     * @return array
     */
    public function getNewRules(): array
    {
        return $this->newRules;
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
            'old' => $this->oldRules,
            'new' => $this->newRules
        ];
    }
}