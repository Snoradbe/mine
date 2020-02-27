<?php


namespace App\Http\Controllers\Client\BugReport;


use App\Entity\Site\BugReportMessage;
use App\Exceptions\Exception;
use App\Handlers\Client\BugReport\SendMessageHandler;
use App\Http\Controllers\Controller;
use App\Repository\Site\BugReport\BugReportRepository;
use App\Repository\Site\BugReportMessage\BugReportMessageRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MessagesController extends Controller
{
    public function load(BugReportRepository $bugReportRepository, BugReportMessageRepository $bugReportMessageRepository, int $reportId)
    {
        $report = $bugReportRepository->find($reportId);
        if (is_null($report)) {
            return new JsonResponse('Репорт не найден!', 500);
        }

        if ($report->getUser() !== Auth::getUser()) {
            return new JsonResponse('Это не ваш репорт!', 500);
        }

        $page = abs((int) request('page', 1));

        $messages = $bugReportMessageRepository->getAll($report, $page);

        return new JsonResponse([
            'messages' => array_map(function (BugReportMessage $bugReportMessage) {
                return $bugReportMessage->toArray();
            }, $messages->all()),
            'pagination' => Utils::paginationData($messages)
        ]);
    }

    public function send(Request $request, SendMessageHandler $handler, int $reportId)
    {
        try {
            $this->validate($request, [
                'message' => 'required|string|max:1000',
            ]);

            $message = $handler->handle(Auth::getUser(), $reportId, $request->post('message'));

            return new JsonResponse([
                'msg' => 'Сообщение отправлено',
                'message' => $message->toArray()
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}