<?php


namespace App\Handlers\Admin\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\BugReportMessage;
use App\Entity\Site\User;
use App\Events\Admin\BugReport\SendMessageEvent;
use App\Exceptions\Exception;
use App\Repository\Site\BugReport\BugReportRepository;
use App\Repository\Site\BugReportMessage\BugReportMessageRepository;

class SendMessageHandler
{
    private $bugReportRepository;

    private $messageRepository;

    public function __construct(BugReportRepository $bugReportRepository, BugReportMessageRepository $messageRepository)
    {
        $this->bugReportRepository = $bugReportRepository;
        $this->messageRepository = $messageRepository;
    }

    private function getReport(int $id): BugReport
    {
        $report = $this->bugReportRepository->find($id);
        if (is_null($report)) {
            throw new Exception('Репорт не найден!');
        }

        return $report;
    }

    public function handle(User $admin, int $reportId, string $message): void
    {
        $report = $this->getReport($reportId);
        if ($report->getStatus() != BugReport::IS_ACTIVE['type']) {
            throw new Exception('Этот репорт уже закрыт, в него нельзя отправить сообщение');
        }

        $message = new BugReportMessage($report, $admin, nl2br(strip_tags($message)));

        $this->messageRepository->create($message);

        event(new SendMessageEvent($admin, $report, $message));
    }
}