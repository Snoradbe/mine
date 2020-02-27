<?php


namespace App\Http\Controllers\Client\Shop;


use App\Entity\Site\Shop\Category;
use App\Entity\Site\Shop\Product;
use App\Exceptions\Exception;
use App\Handlers\Client\Shop\BuyHandler;
use App\Handlers\Client\Shop\LoadProductsHandler;
use App\Http\Controllers\Controller;
use App\Repository\Site\Shop\Category\CategoryRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Services\Shop\Enchants\Enchant;
use App\Services\Shop\Enchants\Enchants;
use App\Services\Shop\Search;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ShopController extends Controller
{
    public function load(CategoryRepository $categoryRepository)
    {
        return new JsonResponse([
            'cancel_fee' => config('site.shop.cancel_fee', 0),
            'categories' => array_map(function (Category $category) {
                return $category->toArray();
            }, $categoryRepository->getAll()),
            'enchants' => Enchants::all()->map(function (Enchant $enchant) {
                return $enchant->toArray();
            })->toArray()
        ]);
    }

    public function loadProducts(Request $request, LoadProductsHandler $handler, int $server, int $page)
    {
        $page = abs($page);
        try {
            $search = new Search(
                $server,
                !empty(trim($request->get('category'))) ? (int) $request->get('category') : null,
                !empty(trim($request->get('name'))) ? $request->get('name') : null,
                !empty(trim($request->get('valute'))) ? $request->get('valute') : null
            );

            $products = $handler->handle($search, $page);

            return new JsonResponse([
                'products' => array_map(function (Product $product) {
                    return $product->toArray();
                }, $products->all()),
                'pagination' => Utils::paginationData($products),
            ]);
        } catch (Exception $exception) {
            return $exception->toJsonResponse();
        }
    }

    public function buy(Request $request, BuyHandler $handler, int $server)
    {
        try {
            $this->validate($request, [
                'product' => 'integer|min:1|required',
                'amount' => 'integer|min:1|required',
                'valute' => 'string|in:rub,coins|required'
            ]);

            $po = $handler->handle(
                Auth::getUser(),
                $server,
                (int) $request->post('product'),
                (int) $request->post('amount'),
                $request->post('valute')
            );

            return new JsonResponse([
                'money' => $po->getUser()->getMoney(),
                'coins' => $po->getUser()->getCoins(),
                'msg' => "Вы успешно купили: " . $po->getProduct()->getProductName()
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->getMessageBag()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }
}