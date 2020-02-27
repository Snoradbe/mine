<?php


namespace App\Http\Controllers\Admin\Applications;


use App\Entity\Site\Application;
use App\Exceptions\Exception;
use App\Handlers\Admin\Applications\ManageHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Application\ApplicationRepository;
use App\Services\Auth\Auth;
use App\Services\Permissions\Permissions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplicationsController extends Controller
{
    public function render(Request $request, ApplicationRepository $applicationRepository, ?int $type = null)
    {
        NavMenu::$active = 'applications';

        $page = abs((int) $request->get('page', 1));

        if (!is_null($type) && ($type < 0 || $type > 3)) {
            $type = null;
        }

        $servers =
            Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_APPLICATIONS_VIEW_ALL)
                ? null
                : Auth::getUser()->permissions()->getServersWithPermission(Permissions::MP_APPLICATIONS_VIEW);

        $name = $request->get('name');
        if (!empty(trim($name))) {
            $applications = $applicationRepository->search($name, $servers, $page, $type);
        } else {
            $applications = $applicationRepository->getAll($type, $servers, $page);
        }

        $canManage = Auth::getUser()->permissions()->hasPermission(Permissions::MP_APPLICATIONS_MANAGE_ALL);

        return view('admin.applications.index', [
            'applications' => $applications,
            'type' => $type,
            'name' => $name,
            'canManage' => $canManage,
            'manageServers' =>
                $canManage
                    ? []
                    : (Auth::getUser()->permissions()->getServersWithPermission(Permissions::MP_APPLICATIONS_MANAGE) ?: [])
        ]);
    }

    public function manage(Request $request, ManageHandler $handler)
    {
        try {
            $this->validate($request, [
                'id' => 'required|integer',
                'accept' => 'nullable',
                'cancel' => 'nullable',
                'again' => 'nullable'
            ]);

            $accept = $request->post('accept');
            $cancel = $request->post('cancel');
            $again = $request->post('again');

            $type = null;
            $typeWord = '';
            if (!empty($accept)) {
                $type = Application::ACCEPT;
                $typeWord = 'одобрили';
            } elseif (!empty($cancel)) {
                $type = Application::CANCEL;
                $typeWord = 'отклонили';
            } elseif (!empty($again)) {
                $type = Application::AGAIN;
                $typeWord = 'отправили на повтор';
            }

            if (is_null($type)) {
                throw new Exception('Тип не выбран!');
            }

            $handler->handle(
                Auth::getUser(),
                (int) $request->post('id'),
                $type
            );

            return redirect()->back()->with('success_message', "Вы успешно $typeWord заявку");
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->validator->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}