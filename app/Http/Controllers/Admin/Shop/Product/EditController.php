<?php


namespace App\Http\Controllers\Admin\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Category;
use App\Entity\Site\Shop\Item;
use App\Exceptions\Exception;
use App\Handlers\Admin\Shop\Product\EditOneHandler;
use App\Handlers\Admin\Shop\Product\EditPacketHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Category\CategoryRepository;
use App\Repository\Site\Shop\Item\ItemRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Services\Shop\Enchants\Enchant;
use App\Services\Shop\Enchants\Enchants;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EditController extends Controller
{
    public function render(
        ProductRepository $productRepository,
        ServerRepository $serverRepository,
        ItemRepository $itemRepository,
        CategoryRepository $categoryRepository,
        int $id)
    {
        NavMenu::$active = 'shop.products';

        $product = $productRepository->find($id);
        if (is_null($product)) {
            return redirect()->back()->withErrors('Товар не найден!');
        }

        return view('admin.shop.products.edit', [
            'product' => $product->toArray(),
            'servers' => array_map(function (Server $server) {
                return $server->toArray();
            }, $serverRepository->getAll(true)),
            'items' => array_map(function (Item $item) {
                return $item->toArray();
            }, $itemRepository->getAll()),
            'categories' => array_map(function (Category $category) {
                return $category->toArray();
            }, $categoryRepository->getAll()),
            'enchants' => Enchants::all()->map(function (Enchant $enchant) {
                return $enchant->toArray();
            })->toArray()
        ]);
    }

    public function editOne(Request $request, EditOneHandler $handler, int $id)
    {
        try {
            $this->validate($request, [
                'server' => 'nullable|integer',
                'category' => 'required|integer',
                'child_category' => 'nullable|integer',
                'item' => 'required|integer',
                'amount' => 'required|integer|min:1',
                'price' => 'required|integer|min:0',
                'price_coins' => 'required|integer|min:0',
                'discount' => 'required|integer|min:0|max:99',
                'discount_date' => 'nullable|date',
                'enchants' => 'nullable|array'
            ]);

            if ($request->post('price') < 1 && $request->post('price_coins') < 1) {
                throw new Exception('Ни одна цена не выбрана!');
            }

            $product = $handler->handle(
                Auth::getUser(),
                $id,
                empty($request->post('server')) ? null : (int) $request->post('server'),
                (int) $request->post('category'),
                is_null($request->post('child_category')) ? null : (int) $request->post('child_category'),
                (int) $request->post('item'),
                (int) $request->post('amount'),
                (int) $request->post('price'),
                (int) $request->post('price_coins'),
                (int) $request->post('discount'),
                $request->post('discount_date'),
                $request->post('enchants', [])
            );

            return new JsonResponse(['product' => $product->toArray(true)]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }

    public function editPacket(Request $request, EditPacketHandler $handler, int $id)
    {
        try {
            $this->validate($request, [
                'server' => 'nullable|integer',
                'category' => 'required|integer',
                'child_category' => 'nullable|integer',
                'items' => 'required|array',
                'name' => 'required|string',
                'price' => 'required|integer|min:0',
                'price_coins' => 'required|integer|min:0',
                'discount' => 'required|integer|min:0|max:99',
                'discount_date' => 'nullable|date',
                'items_enchants' => 'nullable|array'
            ]);

            if ($request->post('price') < 1 && $request->post('price_coins') < 1) {
                throw new Exception('Ни одна цена не выбрана!');
            }

            if (empty($request->post('items'))) {
                throw new Exception('Ни один итем не выбран!');
            }

            $product = $handler->handle(
                Auth::getUser(),
                $id,
                empty($request->post('server')) ? null : (int) $request->post('server'),
                (int) $request->post('category'),
                is_null($request->post('child_category')) ? null : (int) $request->post('child_category'),
                $request->post('items'),
                $request->post('name'),
                (int) $request->post('price'),
                (int) $request->post('price_coins'),
                (int) $request->post('discount'),
                $request->post('discount_date'),
                $request->post('items_enchants', [])
            );

            return new JsonResponse(['product' => $product->toArray(true)]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }

    public function enable(EditOneHandler $handler, int $id)
    {
        try {
            $enabled = $handler->enable($id);

            return new JsonResponse(['enabled' => $enabled]);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }
}