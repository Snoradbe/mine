<?php


namespace App\Http\Controllers\Admin\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Services\Response\JsonResponse;
use App\Services\Shop\Search;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ListController extends Controller
{
    public function render(ServerRepository $serverRepository)
    {
        NavMenu::$active = 'shop.products';

        return view('admin.shop.products.list', [
            'servers' => array_map(function (Server $server) {
                return $server->toArray();
            }, $serverRepository->getAll(false))
        ]);
    }

    public function loadProducts(Request $request, ProductRepository $productRepository, int $page = 1)
    {
        try {
            $this->validate($request, [
                'name' => 'nullable'
            ]);

            $search = new Search(
                null,
                null,
                empty(trim($request->post('name'))) ? null : $request->post('name')
            );

            $products = $productRepository->setPerPage(50)->getAll($search, false, $page);

            return new JsonResponse([
                'products' => array_map(function (Product $product) {
                    return $product->toArray(true);
                }, $products->all()),
                'pagination' => Utils::paginationData($products)
            ]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        }
    }
}