<?php


namespace App\Http\Controllers\Client\Applications;


use App\Entity\Site\Application;
use App\Exceptions\Exception;
use App\Handlers\Client\Applications\ParseFormHandler;
use App\Handlers\Client\Applications\SendHandler;
use App\Http\Controllers\Controller;
use App\Repository\Site\Application\ApplicationRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Services\Settings\DataType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplicationsController extends Controller
{
    public function load(ServerRepository $serverRepository, ApplicationRepository $applicationRepository)
    {
        $list = [];
        $servers = $serverRepository->getAll();
        $settings = settings('applications', DataType::JSON, []);

        $cooldown = $settings['cooldown'] ?? 9999;
        $statuses = $settings['statuses'];

        $statusNames = [];

        foreach ($statuses as $status => $data)
        {
            $statusNames[$status] = $data['name'];

            $list[$status] = [
                'name' => $data['name'],
                'servers' => []
            ];
            foreach ($servers as $server)
            {
                if (isset($data['enabled'][$server->getId()]) && $data['enabled'][$server->getId()]) {
                    $list[$status]['servers'][] = $server->toArray();
                }
            }

            if (empty($list[$status]['servers'])) {
                unset($list[$status]);
            }
        }

        $last = $applicationRepository->findLast(Auth::getUser());
        $groupName = null;
        if (!is_null($last)) {
            $groupName = $statusNames[$last->getPosition()] ?? null;
        }
        $last = is_null($last) ? null : $last->toArray($cooldown);
        if (!is_null($last) && !empty($groupName)) {
            $last['position'] = $groupName;
        }

        return new JsonResponse([
            'list' => $list,
            'last' => $last,
            'statuses' => [
                Application::WAIT => 'wait',
                Application::ACCEPT => 'accept',
                Application::CANCEL => 'cancel',
                Application::AGAIN => 'again',
            ],
            'cooldown' => $cooldown
        ]);
    }

    public function loadForm(Request $request, ParseFormHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'group' => 'required|string'
            ]);

            [$status] = $handler->handle($request->post('group'), $server);

            return new JsonResponse([
                'data' => $status
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse([
                'msg' => $exception->validator->errors()->first()
            ], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }

    public function send(Request $request, SendHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'group' => 'required|string',
                'answers' => 'required|array',
                'server_answers' => 'nullable|array'
            ]);

            $handler->handle(
                Auth::getUser(),
                $request->post('group'),
                $server,
                $request->post('answers'),
                $request->post('server_answers', [])
            );

            return new JsonResponse([
                'msg' => 'Вы успешно подали заявку. Результат вы сможете увидеть на этой же странице'
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse([
                'msg' => $exception->validator->errors()->first()
            ], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}