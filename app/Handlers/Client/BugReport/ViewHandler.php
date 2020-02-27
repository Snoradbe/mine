<?php


namespace App\Handlers\Client\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Repository\Site\BugReport\BugReportRepository;

class ViewHandler
{
    private $bugReportRepository;

    public function __construct(BugReportRepository $bugReportRepository)
    {
        $this->bugReportRepository = $bugReportRepository;
    }

    private function getBugReport(int $id): BugReport
    {
        $report = $this->bugReportRepository->find($id);
        if (is_null($report)) {
            throw new Exception('Репорт не найден!');
        }

        return $report;
    }

    public function handle(User $user, int $id): BugReport
    {
        $report = $this->getBugReport($id);
        if ($report->getUser() !== $user) {
            throw new Exception('Это не ваш репорт!');
        }

        if (!$report->isRead() && $report->getLastUser() !== $user) {
            $report->setRead(true);
            $this->bugReportRepository->update($report);
        }

        return $report;
    }
}