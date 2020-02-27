<?php


namespace App\Events\Admin\Applications\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use Illuminate\Contracts\Support\Arrayable;

class EditDescriptionEvent implements Arrayable
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
    private $oldDescription;

    /**
     * @var string
     */
    private $newDescription;

    /**
     * EditDescriptionEvent constructor.
     * @param User $admin
     * @param Group $group
     * @param string $oldDescription
     * @param string $newDescription
     */
    public function __construct(User $admin, Group $group, string $oldDescription, string $newDescription)
    {
        $this->admin = $admin;
        $this->group = $group;
        $this->oldDescription = $oldDescription;
        $this->newDescription = $newDescription;
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
    public function getOldDescription(): string
    {
        return $this->oldDescription;
    }

    /**
     * @return string
     */
    public function getNewDescription(): string
    {
        return $this->newDescription;
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
            'old' => $this->oldDescription,
            'new' => $this->newDescription
        ];
    }
}