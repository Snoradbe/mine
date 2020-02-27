<?php


namespace App\Events\Admin\Applications;


use App\Entity\Site\Application;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class ManageEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var int
     */
    private $status;

    /**
     * ManageEvent constructor.
     * @param User $admin
     * @param Application $application
     * @param int $status
     */
    public function __construct(User $admin, Application $application, int $status)
    {
        $this->admin = $admin;
        $this->application = $application;
        $this->status = $status;
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function getStatusName(): string
    {
        return Application::getStatusName($this->status);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'application' => $this->application->toArray(),
            'status' => $this->status,
            'status_name' => $this->getStatusName()
        ];
    }
}