<?php


namespace App\Http\Controllers\Admin\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Category;
use App\Entity\Site\Shop\Item;
use App\Exceptions\Exception;
use App\Handlers\Admin\Shop\Product\AddOneHandler;
use App\Handlers\Admin\Shop\Product\AddPacketHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Category\CategoryRepository;
use App\Repository\Site\Shop\Item\ItemRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Services\Shop\Enchants\Enchant;
use App\Services\Shop\Enchants\Enchants;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AddController extends Controller
{
    public function render(ServerRepository $serverRepository, ItemRepository $itemRepository, CategoryRepository $categoryRepository)
    {
        NavMenu::$active = 'shop.products';

        return view('admin.shop.products.add', [
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

    public function addOne(Request $request, AddOneHandler $handler)
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
                'enchants' => 'nullable|array'
            ]);

            if ($request->post('price') < 1 && $request->post('price_coins') < 1) {
                throw new Exception('Ни одна цена не выбрана!');
            }

            $handler->handle(
                Auth::getUser(),
                empty($request->post('server')) ? null : (int) $request->post('server'),
                (int) $request->post('category'),
                is_null($request->post('child_category')) ? null : (int) $request->post('child_category'),
                (int) $request->post('item'),
                (int) $request->post('amount'),
                (int) $request->post('price'),
                (int) $request->post('price_coins'),
                $request->post('enchants', [])
            );

            return new JsonResponse(['status' => 'ok']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }

    public function addPacket(Request $request, AddPacketHandler $handler)
    {
        try {
            //throw new Exception(implode(',', array_keys($request->post())));
            $this->validate($request, [
                'server' => 'nullable|integer',
                'category' => 'required|integer',
                'child_category' => 'nullable|integer',
                'items' => 'required|array',
                'name' => 'required|string|min:2|max:255',
                'price' => 'required|integer|min:0',
                'price_coins' => 'required|integer|min:0',
                'img_file' => 'required_without:img_url|file',
                'img_url' => 'required_without:img_file|url',
                'items_enchants' => 'nullable|array'
            ]);

            if ($request->post('price') < 1 && $request->post('price_coins') < 1) {
                throw new Exception('Ни одна цена не выбрана!');
            }

            if (empty($request->post('items'))) {
                throw new Exception('Ни один итем не выбран!');
            }

            if (is_null($request->file('img_file')) && empty(trim($request->post('img_url', '')))) {
                throw new Exception('Картинка не выбрана!');
            }

            $handler->handle(
                Auth::getUser(),
                $request->file('img_file'),
                $request->post('img_url'),
                empty($request->post('server')) ? null : (int) $request->post('server'),
                (int) $request->post('category'),
                is_null($request->post('child_category')) ? null : (int) $request->post('child_category'),
                $request->post('name'),
                $request->post('items'),
                (int) $request->post('price'),
                (int) $request->post('price_coins'),
                $request->post('items_enchants', [])
            );

            return new JsonResponse(['status' => 'ok']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }
}