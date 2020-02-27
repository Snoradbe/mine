<?php


namespace App\Events\Client\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Event;

class SendReportEvent extends ClientEvent implements Event
{
    /**
     * @var BugReport
     */
    private $report;

    /**
     * SendReportEvent constructor.
     * @param User $user
     * @param BugReport $report
     */
    public function __construct(User $user, BugReport $report)
    {
        parent::__construct($user);

        $this->report = $report;
    }

    /**
     * @return BugReport
     */
    public function getReport(): BugReport
    {
        return $this->report;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'report' => $this->report->toArray()
        ];
    }
}