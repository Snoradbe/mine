<?php


namespace App\Http\Controllers\Client\Cabinet;


use App\Exceptions\Exception;
use App\Handlers\Client\Cabinet\BuyGroupHandler;
use App\Http\Controllers\Controller;
use App\Services\Auth\Auth;
use App\Services\Cabinet\CabinetSettings;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GroupsController extends Controller
{
    public function buy(Request $request, BuyGroupHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'type' => 'required|string|in:primary,other'
            ]);

            $groups = CabinetSettings::getSellingGroups($request->post('type') == 'primary', $server);
            if (empty($groups) || !is_array($groups)) {
                throw new Exception('На этот сервер нет групп для продажи!');
            }

            $this->validate($request, [
                'group' => 'required|in:' . implode(',', array_keys($groups))
            ]);

            $group = $request->post('group');

            $periods = isset($groups[$group]) ? $groups[$group] : null;
            if (empty($periods) || !is_array($periods)) {
                throw new Exception('На этот сервер нет периодов для продажи!');
            }

            $this->validate($request, [
                'period' => 'required|integer|in:' . implode(',', array_keys($periods))
            ]);

            $price = $periods[(int) $request->post('period')];

            $userGroup = $handler->handle(
                Auth::getUser(),
                $server,
                $request->post('group'),
                (int) $request->post('period'),
                $price
            );

            return new JsonResponse([
                'msg' => 'Вы успешно купили группу',
                'price' => $price,
                'user_group' => [
                    'id' => $userGroup->getId(),
                    'expire' => $userGroup->getExpireAt(),
                    'start' => $userGroup->getCreatedAt(),
                    'group' => $userGroup->getGroup()->toArray()
                ]
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }
}