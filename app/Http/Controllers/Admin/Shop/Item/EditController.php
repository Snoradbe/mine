<?php


namespace App\Http\Controllers\Admin\Shop\Item;


use App\Entity\Site\Shop\ItemType;
use App\Exceptions\Exception;
use App\Handlers\Admin\Shop\Item\EditHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Shop\Item\ItemRepository;
use App\Repository\Site\Shop\ItemType\ItemTypeRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EditController extends Controller
{
    public function render(ItemTypeRepository $itemTypeRepository, ItemRepository $itemRepository, int $id)
    {
        NavMenu::$active = 'shop.items';

        $item = $itemRepository->find($id);
        if (is_null($item)) {
            return redirect()->back()->withErrors('Итем не найден!');
        }

        return view('admin.shop.items.edit', [
            'types' => array_map(function (ItemType $type) {
                return $type->toArray();
            }, $itemTypeRepository->getAll()),
            'item' => $item->toArray()
        ]);
    }

    public function edit(Request $request, EditHandler $handler, int $id)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|min:3|max:255',
                'descr' => 'nullable',
                'type' => 'required|string',
                'id' => 'required|regex:/(^[A-Za-z0-9\:]+)/',
            ]);

            $item = $handler->handle(
                Auth::getUser(),
                $id,
                $request->post('type'),
                $request->post('name'),
                $request->post('descr'),
                $request->post('id')
            );

            return new JsonResponse(['item' => $item->toArray()]);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }
}