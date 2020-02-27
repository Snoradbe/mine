<?php


namespace App\Handlers\Admin\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\User;
use App\Events\Admin\BugReport\ChangeStatusEvent;
use App\Exceptions\Exception;
use App\Repository\Site\BugReport\BugReportRepository;

class ChangeStatusHandler
{
    private $bugReportRepository;

    public function __construct(BugReportRepository $bugReportRepository)
    {
        $this->bugReportRepository = $bugReportRepository;
    }

    private function getReport(int $id): BugReport
    {
        $report = $this->bugReportRepository->find($id);
        if (is_null($report)) {
            throw new Exception('Репорт не найден!');
        }

        return $report;
    }

    public function handle(User $admin, int $reportId, int $status): void
    {
        $report = $this->getReport($reportId);
        if ($report->getStatus() != BugReport::IS_ACTIVE['type']) {
            throw new Exception('Этот репорт уже закрыт, его нельзя снова открыть!');
        }

        $report->setStatus($status);
        $this->bugReportRepository->update($report);

        event(new ChangeStatusEvent($admin, $report, $status));
    }
}