<?php


namespace App\Http\Controllers\Admin\BugReport;


use App\Entity\Site\BugReport;
use App\Exceptions\Exception;
use App\Handlers\Admin\BugReport\ChangeStatusHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\BugReport\BugReportRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BugReportController extends Controller
{
    public function render(BugReportRepository $bugReportRepository)
    {
        NavMenu::$active = 'bugreports';

        $page = abs((int) request('page', 1));

        $types = [];
        foreach (BugReport::BUGS as $type)
        {
            $types[$type['type']] = $type['desc'];
        }

        $statuses = [];
        foreach (BugReport::TYPES as $status)
        {
            $statuses[$status['type']] = $status['desc'];
        }

        return view('admin.bugreport.list', [
            'reports' => $bugReportRepository->getAll($page),
            'types' => $types,
            'statuses' => $statuses,
        ]);
    }

    public function changeStatus(Request $request, ChangeStatusHandler $handler, int $id)
    {
        try {
            $this->validate($request, [
                'type' => 'required|integer|in:' . implode(',', array_map(function (array $type) {return $type['type'];}, BugReport::TYPES)),
            ]);

            $handler->handle(Auth::getUser(), $id, (int) $request->post('type'));

            return redirect()->back()->with('success_message', 'Статус изменен');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack()->withInput();
        }
    }
}