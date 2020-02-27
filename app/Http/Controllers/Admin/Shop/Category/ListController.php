<?php


namespace App\Http\Controllers\Admin\Shop\Category;


use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Shop\Category\CategoryRepository;

class ListController extends Controller
{
    public function render(CategoryRepository $categoryRepository)
    {
        NavMenu::$active = 'shop.categories';

        return view('admin.shop.categories.list', [
            'categories' => $categoryRepository->getAll()
        ]);
    }
}