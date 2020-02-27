<?php


namespace App\Events\Admin\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class ChangeStatusEvent implements Event
{
    use GetAdminIP;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var BugReport
     */
    private $report;

    /**
     * @var int
     */
    private $status;

    /**
     * ChangeStatusEvent constructor.
     * @param User $admin
     * @param BugReport $report
     * @param int $status
     */
    public function __construct(User $admin, BugReport $report, int $status)
    {
        $this->admin = $admin;
        $this->report = $report;
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
     * @return BugReport
     */
    public function getReport(): BugReport
    {
        return $this->report;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'report' => $this->report->toArray(),
            'status' => $this->status
        ];
    }
}