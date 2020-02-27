<?php


namespace App\Events\Admin\Applications\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class EditServerSelfFormEvent implements Event
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
    private $oldForm;

    /**
     * @var array
     */
    private $newForm;

    /**
     * EditServerFormEvent constructor.
     * @param User $admin
     * @param Group $group
     * @param array $oldForm
     * @param array $newForm
     */
    public function __construct(User $admin, Group $group, array $oldForm, array $newForm)
    {
        $this->admin = $admin;
        $this->group = $group;
        $this->oldForm = $oldForm;
        $this->newForm = $newForm;
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
    public function getOldForm(): array
    {
        return $this->oldForm;
    }

    /**
     * @return array
     */
    public function getNewForm(): array
    {
        return $this->newForm;
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
            'old' => $this->oldForm,
            'new' => $this->newForm
        ];
    }
}