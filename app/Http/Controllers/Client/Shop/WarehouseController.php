<?php


namespace App\Http\Controllers\Client\Shop;


use App\Entity\Game\Shop\RealMine;
use App\Exceptions\Exception;
use App\Handlers\Client\Shop\Warehouse\CancelPurchaseHandler;
use App\Handlers\Client\Shop\Warehouse\LoadHandler;
use App\Http\Controllers\Controller;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Utils;

class WarehouseController extends Controller
{
    public function load(LoadHandler $handler, int $server, int $page)
    {
        if ($page < 1) {
            $page = 1;
        }

        try {
            $warehouse = $handler->handle(Auth::getUser(), $server, $page);

            return new JsonResponse([
                'products' => array_map(function (RealMine $realMine) {
                    return $realMine->toArray();
                }, $warehouse->all()),
                'pagination' => Utils::paginationData($warehouse)
            ]);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }

    public function cancelPurchase(CancelPurchaseHandler $handler, int $id)
    {
        try {
            [$price, $valute] = $handler->handle(Auth::getUser(), $id);

            return new JsonResponse([
                'price' => $price,
                'valute' => $valute,
            ]);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }
}