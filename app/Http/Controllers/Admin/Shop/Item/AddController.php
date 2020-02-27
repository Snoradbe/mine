<?php


namespace App\Http\Controllers\Admin\Shop\Item;


use App\Entity\Site\Shop\ItemType;
use App\Exceptions\Exception;
use App\Handlers\Admin\Shop\Item\AddHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Shop\ItemType\ItemTypeRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AddController extends Controller
{
    public function render(ItemTypeRepository $itemTypeRepository)
    {
        NavMenu::$active = 'shop.items';

        return view('admin.shop.items.add', [
            'types' => array_map(function (ItemType $type) {
                return $type->toArray();
            }, $itemTypeRepository->getAll())
        ]);
    }

    public function add(Request $request, AddHandler $handler)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|min:3|max:255',
                'descr' => 'nullable',
                'type' => 'required|string',
                'id' => 'required|regex:/(^[A-Za-z0-9\:]+)/',
                'img_file' => 'required_without:img_url|file',
                'img_url' => 'required_without:img_file|url'
            ]);

            $handler->handle(
                Auth::getUser(),
                $request->file('img_file'),
                $request->post('img_url'),
                $request->post('type'),
                $request->post('name'),
                $request->post('descr'),
                $request->post('id')
            );

            return new JsonResponse(['status' => 'ok']);
        } catch (ValidationException $exception) {
            return new JsonResponse(['msg' => $exception->validator->errors()->first()], 500);
        } catch (Exception $exception) {
            return new JsonResponse(['msg' => $exception->getMessage()], 500);
        }
    }
}