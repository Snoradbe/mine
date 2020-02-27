<?php


namespace App\Http\Controllers\Admin\Shop;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Exceptions\Exception;
use App\Handlers\Admin\Shop\Product\DiscountHandler;
use App\Handlers\Admin\Shop\Product\RandomDiscountHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Services\Settings\DataType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DiscountsController extends Controller
{
    public function render(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'shop.discounts';

        return view('admin.shop.discounts', [
            'servers' => array_map(function (Server $server) {
                return $server->toArray();
            }, $serverRepository->getAll(true)),
            'methods' => settings('shop', DataType::JSON, [])['random_discounts'] ?? []
        ]);
    }

    public function loadDiscountProducts(ProductRepository $productRepository)
    {
        return new JsonResponse([
            'products' => array_map(function (Product $product) {
                return $product->toArray(false);
            }, $productRepository->getAllWithDiscount())
        ]);
    }

    public function setDiscount(Request $request, DiscountHandler $handler, int $id)
    {
        try {
            $this->validate($request, [
                'discount' => 'required|integer|min:0|max:99',
                'date' => 'required|date'
            ]);

            [$discount, $date] = $handler->handle(
                Auth::getUser(),
                $id,
                (int) $request->post('discount'),
                $request->post('date')
            );

            return new JsonResponse(compact('discount', 'date'));
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }

    public function random(Request $request, RandomDiscountHandler $handler)
    {
        try {
            $methods = settings('shop', DataType::JSON, [])['random_discounts'] ?? [];

            $this->validate($request, [
                'min' => 'required|integer|min:1|max:99',
                'max' => 'required|integer|min:1|max:99',
                'server' => 'required|integer|min:0',
                'days' => 'required|integer|min:1|max:365',
                'method' => 'required|integer|min:0|max:' . (count($methods) - 1),
            ]);

            if ($request->post('min') >= $request->post('max')) {
                throw new Exception('Неверный диапозон скидки!');
            }

            $handler->handle(
                Auth::getUser(),
                (int) $request->post('min'),
                (int) $request->post('max'),
                (int) $request->post('server'),
                (int) $request->post('days'),
                (int) $request->post('method')
            );

            return new JsonResponse(['status' => 'ok']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }
}