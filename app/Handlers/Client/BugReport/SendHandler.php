<?php


namespace App\Handlers\Client\BugReport;


use App\Entity\Site\BugReport;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Client\BugReport\SendReportEvent;
use App\Exceptions\Exception;
use App\Repository\Site\BugReport\BugReportRepository;
use App\Repository\Site\Server\ServerRepository;

class SendHandler
{
    private $bugReportRepository;

    private $serverRepository;

    public function __construct(BugReportRepository $bugReportRepository, ServerRepository $serverRepository)
    {
        $this->bugReportRepository = $bugReportRepository;
        $this->serverRepository = $serverRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $user, int $serverId, int $type, string $title, string $message): BugReport
    {
        $server = $this->getServer($serverId);

        $countToday = $this->bugReportRepository->getCountToday($user);
        if ($countToday > config('site.bugreport.max_today', 1)) {
            throw new Exception('Вы больше не можете отправлять баги сегодня');
        }

        $bugReport = new BugReport(
            $user,
            $server,
            $type,
            strip_tags($title),
            nl2br(strip_tags($message))
        );

        $this->bugReportRepository->create($bugReport);

        event(new SendReportEvent($user, $bugReport));

        return $bugReport;
    }
}