<?php


namespace App\Handlers\Admin\Shop\Item;


use App\Entity\Site\Shop\Item;
use App\Entity\Site\Shop\ItemType;
use App\Entity\Site\User;
use App\Events\Admin\Shop\Item\AddItemEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Shop\Item\ItemRepository;
use App\Repository\Site\Shop\ItemType\ItemTypeRepository;
use App\Services\Shop\ImageUploader;
use Illuminate\Http\UploadedFile;

class AddHandler
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

    public function handle(User $admin, ?UploadedFile $imgFile, ?string $imgUrl, string $type, string $name, ?string $descr, string $dataId): Item
    {
        if (is_null($imgFile) && empty($imgUrl)) {
            throw new Exception('Картинка не выбрана!');
        }

        $type = $this->getType($type);

        $uploader = new ImageUploader($type->getId(), $dataId);

        $item = new Item($type, $name, $descr, $dataId);

        $this->itemRepository->create($item);

       event(new AddItemEvent($admin, $item));

        if (!is_null($imgFile)) {
            $uploader->uploadByFile($imgFile);
        } else {
            $uploader->uploadByUrl($imgUrl);
        }

        return $item;
    }
}