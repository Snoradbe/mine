<?php


namespace App\Repository\Site\BugReportMessage;


use App\Entity\Site\BugReport;
use App\Entity\Site\BugReportMessage;
use App\Repository\PaginatedDoctrineConstructor;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineBugReportMessageRepository implements BugReportMessageRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    public function getAll(BugReport $bugReport, int $page): LengthAwarePaginator
    {
        return $this->paginate(
            $this->createQueryBuilder('bugm')
                ->where('bugm.bugReport = :report')
                ->setParameter('report', $bugReport)
                ->orderBy('bugm.id', 'DESC')
                ->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );
    }

    public function find(int $id): ?BugReportMessage
    {
        return $this->er->find($id);
    }

    public function create(BugReportMessage $bugReportMessage): void
    {
        $this->em->persist($bugReportMessage);
        $this->em->flush();
    }

    public function delete(BugReportMessage $bugReportMessage): void
    {
        $this->em->remove($bugReportMessage);
        $this->em->flush();
    }
}