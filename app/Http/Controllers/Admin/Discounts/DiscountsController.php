<?php


namespace App\Http\Controllers\Admin\Discounts;


use App\Exceptions\Exception;
use App\Handlers\Admin\Discounts\AddDiscountHandler;
use App\Handlers\Admin\Discounts\DeleteDiscountHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Discounts\AddDiscountRequest;
use App\NavMenu;
use App\Repository\Site\Discount\DiscountRepository;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DiscountsController extends Controller
{
    public function render(
        DiscountRepository $discountRepository,
        ServerRepository $serverRepository,
        GroupRepository $groupRepository)
    {
        NavMenu::$active = 'admin.discounts';

        $massDiscounts = [];

        $discounts = [];

        foreach ($discountRepository->getAll() as $discount)
        {
            $server = $discount->getServer();
            if (is_null($server)) {
                $massDiscounts[] = $discount;
            } else {
                if (!isset($discounts[$server->getName()])) {
                    $discounts[$server->getName()] = [];
                }

                $discounts[$server->getName()][] = $discount;
            }
        }

        return view('admin.discounts.list', [
            'massDiscounts' => $massDiscounts,
            'discounts' => $discounts,
            'servers' => $serverRepository->getAll(false),
            'modules' => config('site.discount.modules', []),
            'groups' => $groupRepository->getAllDonate(),
            'typeName' => function (string $type) {
                switch ($type)
                {
                    case 'all': return 'Все модули';
                    case 'groups_all': return 'Все группы';
                    case 'groups_primary': return 'Основные группы';
                    case 'groups_other': return 'Дополнительные группы';
                }

                if (Str::startsWith($type, 'module_')) {
                    return 'Модуль: ' . str_replace('module_', '', $type);
                }

                if (Str::startsWith($type, 'group_')) {
                    return 'Группа: ' . str_replace('group_', '', $type);
                }

                return $type;
            }
        ]);
    }

    public function add(AddDiscountRequest $request, AddDiscountHandler $handler)
    {
        try {
            $handler->handle(
                Auth::getUser(),
                $request->post('server'),
                (int) $request->post('discount'),
                $request->post('type'),
                $request->post('date')
            );

            return redirect()->back()->withInput()->with('success_message', 'Скидка добавлена');
        } catch (Exception $exception) {
            return $exception->redirectBack()->withInput();
        }
    }

    public function delete(Request $request, DeleteDiscountHandler $handler)
    {
        try {
            $this->validate($request, [
                'id' => 'required|integer'
            ]);

            $handler->handle(Auth::getUser(), (int) $request->post('id'));

            return redirect()->back()->with('success_message', 'Скидка удалена');
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        } catch (Exception $exception) {
            return $exception->redirectBack();
        }
    }
}