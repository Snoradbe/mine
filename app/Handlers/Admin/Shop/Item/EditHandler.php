<?php


namespace App\Handlers\Admin\Shop\Item;


use App\Entity\Site\Shop\Item;
use App\Entity\Site\Shop\ItemType;
use App\Entity\Site\User;
use App\Events\Admin\Shop\Item\EditItemEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Shop\Item\ItemRepository;
use App\Repository\Site\Shop\ItemType\ItemTypeRepository;
use App\Services\Shop\ImageUploader;

class EditHandler
{
    private $itemRepository;

    private $itemTypeRepository;

    public function __construct(ItemRepository $itemRepository, ItemTypeRepository $itemTypeRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->itemTypeRepository = $itemTypeRepository;
    }

    private function getType(string $id): ItemType
    {
        $type = $this->itemTypeRepository->find($id);
        if (is_null($type)) {
            throw new Exception('Тип не найден!');
        }

        return $type;
    }

    private function getItem(int $id): Item
    {
        $item = $this->itemRepository->find($id);
        if (is_null($item)) {
            throw new Exception('Итем не найден!');
        }

        return $item;
    }

    public function handle(User $admin, int $itemId, string $type, string $name, ?string $descr, string $dataId): Item
    {
        $item = $this->getItem($itemId);
        $old = clone $item;

        $oldType = $item->getType()->getId();

        $rename = false;

        if ($oldType != $type) {
            $type = $this->getType($type);
            $item->setType($type);
            $rename = true;
        }

        $item->setName($name);
        $item->setDescription($descr);

        $oldDataId = $item->getDataId();

        if ($oldDataId != $dataId) {
            $item->setDataId($dataId);
            $rename = true;
        }

        if ($rename) {
            $uploader = new ImageUploader($oldType, $oldDataId, false);
            $uploader->rename($item->getType()->getId(), $item->getDataId());
        }

        $this->itemRepository->update($item);

        event(new EditItemEvent($admin, $item, $old));

        return $item;
    }
}