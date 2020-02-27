<?php


namespace App\Events\Admin\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\BugReportMessage;
use App\Entity\Site\User;
use App\Events\Admin\GetAdminIP;
use App\Events\Event;

class SendMessageEvent implements Event
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
     * @var BugReportMessage
     */
    private $message;

    /**
     * SendMessageEvent constructor.
     * @param User $admin
     * @param BugReport $report
     * @param BugReportMessage $message
     */
    public function __construct(User $admin, BugReport $report, BugReportMessage $message)
    {
        $this->admin = $admin;
        $this->report = $report;
        $this->message = $message;
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
     * @return BugReportMessage
     */
    public function getMessage(): BugReportMessage
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'report' => $this->report->toArray(),
            'message' => $this->message->toArray()
        ];
    }
}