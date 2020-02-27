<?php


namespace App\Http\Controllers\Admin\Shop;


use App\Entity\Game\Shop\RealMine;
use App\Entity\Site\Server;
use App\Exceptions\Exception;
use App\Handlers\Admin\Shop\Player\GiveProductHandler;
use App\Handlers\Admin\Shop\Player\Warehouse\ListHandler;
use App\Handlers\Admin\Shop\Player\Warehouse\RemovePurchaseHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Services\Settings\DataType;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlayerController extends Controller
{
    public function giveProduct(Request $request, GiveProductHandler $handler, int $id)
    {
        try {
            $this->validate($request, [
                'server' => 'required|integer|min:1',
                'name' => 'required|string|min:3',
                'amount' => 'required|integer|min:1|max:99'
            ]);

            $handler->handle(
                Auth::getUser(),
                (int) $request->post('server'),
                $request->post('name'),
                $id,
                (int) $request->post('amount')
            );

            return new JsonResponse(['status' => 'ok']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }

    public function warehouse(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'shop.player_warehouse';

        return view('admin.shop.player.warehouse', [
            'servers' => array_map(function (Server $server) {
                return $server->toArray();
            }, $serverRepository->getAll(false)),
            'cancelTypes' => settings('shop', DataType::JSON, [])['cancel_types'] ?? []
        ]);
    }

    public function getWarehouse(Request $request, ListHandler $handler)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|min:2',
                'server' => 'required|integer|min:1'
            ]);

            $page = (int) $request->get('page', 1);
            if ($page < 1) {
                $page = 1;
            }

            $warehouse = $handler->handle(
                $request->post('name'),
                (int) $request->post('server'),
                $page
            );

            return new JsonResponse([
                'products' => array_map(function (RealMine $realMine) {
                    return $realMine->toArray(true);
                }, $warehouse->all()),
                'pagination' => Utils::paginationData($warehouse)
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }

    public function cancelPurchase(Request $request, RemovePurchaseHandler $handler, int $id)
    {
        try {
            $this->validate($request, [
                'type' => 'required|string|in:' . implode(',', array_keys(settings('shop', DataType::JSON, [])['cancel_types'] ?? []))
            ]);

            $handler->handle(Auth::getUser(), $id, $request->post('type'));

            return new JsonResponse(['status' => 'ok']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }
}