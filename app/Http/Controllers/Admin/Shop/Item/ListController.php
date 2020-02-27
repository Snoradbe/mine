<?php


namespace App\Http\Controllers\Admin\Shop\Item;


use App\Entity\Site\Shop\Item;
use App\NavMenu;
use App\Repository\Site\Shop\Item\ItemRepository;

class ListController
{
    public function render(ItemRepository $itemRepository)
    {
        NavMenu::$active = 'shop.items';

        /*if ($page < 1) {
            $page = 1;
        }*/

        return view('admin.shop.items.list', [
            'items' => array_map(function (Item $item) {
                return $item->toArray();
            }, $itemRepository->getAll())
        ]);
    }
}