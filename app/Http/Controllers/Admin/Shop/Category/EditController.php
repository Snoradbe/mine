<?php


namespace App\Http\Controllers\Admin\Shop\Category;


use App\Exceptions\Exception;
use App\Handlers\Admin\Shop\Category\EditHandler;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Category\CategoryRepository;
use App\Services\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EditController extends Controller
{
    public function render(CategoryRepository $categoryRepository, ServerRepository $serverRepository, int $id)
    {
        NavMenu::$active = 'shop.categories';

        $category = $categoryRepository->find($id);
        if (is_null($category)) {
            return redirect()->back()->withErrors('Категория не найдена!');
        }

        return view('admin.shop.categories.edit', [
            'category' => $category,
            'servers' => $serverRepository->getAll(true),
            'categories' => $categoryRepository->getAllParents()
        ]);
    }

    public function edit(Request $request, EditHandler $handler, int $id)
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
                $id,
                empty($request->post('parent')) ? null : (int) $request->post('parent'),
                empty($request->post('server')) ? null : (int) $request->post('server'),
                $request->post('name'),
                (int) $request->post('weight')
            );

            return redirect()->back()->with('success_message', 'Категория была изменена');
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->validator->errors()->first());
        } catch (Exception $exception) {
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
    }
}