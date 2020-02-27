<?php


namespace App\Http\Controllers\Admin\BugReport;


use App\Exceptions\Exception;
use App\Handlers\Admin\BugReport\SendMessageHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\BugReport\BugReportRepository;
use App\Repository\Site\BugReportMessage\BugReportMessageRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function render(BugReportRepository $bugReportRepository, BugReportMessageRepository $messageRepository, int $id)
    {
        NavMenu::$active = 'bugreports';

        $report = $bugReportRepository->find($id);
        if (is_null($report)) {
            return redirect()->route('admin.bugreports')->withErrors('Репорт не найлен!');
        }

        $page = abs((int) request('page', 1));

        $messages = $messageRepository->getAll($report, $page);

        return view('admin.bugreport.full', [
            'report' => $report,
            'messages' => $messages
        ]);
    }

    public function send(Request $request, SendMessageHandler $handler, int $id)
    {
        try {
            $this->validate($request, [
                'message' => 'required|string|min:2|max:1000'
            ]);

            $handler->handle(Auth::getUser(), $id, $request->post('message'));

            return redirect()->back()->with('success_message', 'Сообщение отправлено');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack()->withInput();
        }
    }
}