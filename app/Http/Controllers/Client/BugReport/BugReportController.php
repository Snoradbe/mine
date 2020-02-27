<?php


namespace App\Http\Controllers\Client\BugReport;


use App\Entity\Site\BugReport;
use App\Exceptions\Exception;
use App\Handlers\Client\BugReport\SendHandler;
use App\Http\Controllers\Controller;
use App\Repository\Site\BugReport\BugReportRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BugReportController extends Controller
{
    public function load(BugReportRepository $bugReportRepository)
    {
        $page = abs((int) request('page', 1));

        $reports = $bugReportRepository->getAll($page);

        return new JsonResponse([
            'reports' => array_map(function (BugReport $report) {
                return $report->toArray();
            }, $reports->all()),
            'pagination' => Utils::paginationData($reports),
            'bug_statuses' => BugReport::TYPES,
            'bug_types' => BugReport::BUGS,
        ]);
    }

    public function send(Request $request, SendHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'type' => 'required|integer|in:' . implode(',', array_map(function (array $type) {return $type['type'];}, BugReport::BUGS)),
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:3000'
            ]);

            $report = $handler->handle(
                Auth::getUser(),
                $server,
                (int) $request->post('type'),
                $request->post('title'),
                $request->post('message')
            );

            return new JsonResponse([
                'msg' => 'Репорт отправлен',
                'report' => $report->toArray()
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}