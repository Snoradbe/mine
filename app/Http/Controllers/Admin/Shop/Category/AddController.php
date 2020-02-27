<?php


namespace App\Http\Controllers\Admin\Shop\Category;


use App\Exceptions\Exception;
use App\Handlers\Admin\Shop\Category\AddHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Category\CategoryRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AddController extends Controller
{
    public function render(ServerRepository $serverRepository, CategoryRepository $categoryRepository)
    {
        NavMenu::$active = 'shop.categories';

        return view('admin.shop.categories.add', [
            'servers' => $serverRepository->getAll(true),
            'categories' => $categoryRepository->getAllParents()
        ]);
    }

    public function add(Request $request, AddHandler $handler)
    {
        try {
            $this->validate($request, [
                'server' => 'nullable|integer',
                'parent' => 'nullable|integer',
                'name' => 'required|string|min:3|max:255',
                'weight' => 'required|integer|min:0|max:127'
            ]);

            $handler->handle(
                Auth::getUser(),
                empty($request->post('server')) ? null : (int) $request->post('server'),
                empty($request->post('parent')) ? null : (int) $request->post('parent'),
                $request->post('name'),
                (int) $request->post('weight')
            );

            return redirect()->route('admin.shop.category_list')->with('success_message', 'Категория была добавлена');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->validator->errors()->first());
        } catch (Exception $exception) {
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
    }
}