<?php


namespace App\Handlers\Client\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\BugReportMessage;
use App\Entity\Site\User;
use App\Events\Client\BugReport\SendMessageEvent;
use App\Exceptions\Exception;
use App\Repository\Site\BugReport\BugReportRepository;
use App\Repository\Site\BugReportMessage\BugReportMessageRepository;

class SendMessageHandler
{
    private $bugReportRepository;

    private $bugReportMessageRepository;

    public function __construct(BugReportRepository $bugReportRepository, BugReportMessageRepository $messageRepository)
    {
        $this->bugReportRepository = $bugReportRepository;
        $this->bugReportMessageRepository = $messageRepository;
    }

    private function getBugReport(int $id): BugReport
    {
        $report = $this->bugReportRepository->find($id);
        if (is_null($report)) {
            throw new Exception('Репорт не найден!');
        }

        return $report;
    }

    public function handle(User $user, int $id, string $message): BugReportMessage
    {
        $report = $this->getBugReport($id);
        if ($report->getUser() !== $user) {
            throw new Exception('Этот репорт не ваш!');
        }

        if ($report->getStatus() != BugReport::IS_ACTIVE['type']) {
            throw new Exception('Этот репорт уже закрыт. Вы не можете добавить комментарий!');
        }

        $message = new BugReportMessage(
            $report,
            $user,
            strip_tags($message)
        );

        $this->bugReportMessageRepository->create($message);

        event(new SendMessageEvent($user, $report, $message));

        return $message;
    }
}