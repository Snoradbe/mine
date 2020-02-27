<?php


namespace App\Events\Admin\Applications\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use Illuminate\Contracts\Support\Arrayable;

class EditGroupEvent implements Arrayable
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
     * @var string
     */
    private $oldName;

    /**
     * @var string
     */
    private $newName;

    /**
     * @var array
     */
    private $oldEnabled;

    /**
     * @var array
     */
    private $newEnabled;

    /**
     * EditGroupEvent constructor.
     * @param User $admin
     * @param Group $group
     * @param string $oldName
     * @param string $newName
     * @param array $oldEnabled
     * @param array $newEnabled
     */
    public function __construct(User $admin, Group $group, string $oldName, string $newName, array $oldEnabled, array $newEnabled)
    {
        $this->admin = $admin;
        $this->group = $group;
        $this->oldName = $oldName;
        $this->newName = $newName;
        $this->oldEnabled = $oldEnabled;
        $this->newEnabled = $newEnabled;
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
     * @return string
     */
    public function getOldName(): string
    {
        return $this->oldName;
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }

    /**
     * @return array
     */
    public function getOldEnabled(): array
    {
        return $this->oldEnabled;
    }

    /**
     * @return array
     */
    public function getNewEnabled(): array
    {
        return $this->newEnabled;
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
            'old_name' => $this->oldName,
            'new_name' => $this->newName,
            'old_enabled' => $this->oldEnabled,
            'new_enabled' => $this->newEnabled
        ];
    }
}