<?php


namespace App\Events\Client\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\BugReportMessage;
use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Event;

class SendMessageEvent extends ClientEvent implements Event
{
    private $report;

    private $message;

    public function __construct(User $user, BugReport $report, BugReportMessage $message)
    {
        parent::__construct($user);

        $this->report = $report;
        $this->message = $message;
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