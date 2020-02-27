<?php


namespace App\Services\Shop;


use App\Entity\Site\Shop\Product;
use App\Exceptions\Exception;
use Illuminate\Http\UploadedFile;

class ImageUploader
{
    private const BASE_PATH = 'uploads/shop/images';

    private $type;

    private $id;

    private $dir;

    private $fileName;

    public function __construct(string $type, string $id, bool $checkExists = true)
    {
        $this->type = $type;
        $this->id = $id;

        $this->init($type, $id, $checkExists);
    }

    private function init(string $type, string $id, bool $checkExists)
    {
        $dir = $this->dir($type);
        $fileName = $this->fileName($id);

        if (!file_exists($dir)) {
            mkdir($dir, 755);
        }

        if ($checkExists && "{$this->dir($this->type)}/{$this->fileName($this->id)}" != "$dir/$fileName" && is_file("$dir/$fileName")) {
            throw new Exception('Файл с таким id предмета уже существует');
        }

        $this->dir = $dir;
        $this->fileName = $fileName;
    }

    public function uploadByFile(UploadedFile $file): void
    {
        $file->move($this->dir, $this->fileName);
    }

    public function uploadByUrl(string $url): void
    {
        if (substr($url, 0, 4) == 'data') {
            $file = base64_decode(str_replace(
                ' ',
                '+',
                str_replace('data:image/png;base64,', '', $url)
            ));
        } else {
            $file = file_get_contents($url);
        }

        file_put_contents($this->dir . '/' . $this->fileName, $file);
    }

    public function rename(string $type, string $id): void
    {
        $oldFile = $this->dir . '/' . $this->fileName;

        if (is_file($oldFile)) {
            $this->init($type, $id, true);

            $newFile = $this->dir . '/' . $this->fileName;

            rename($oldFile, $newFile);
        }
    }

    public function cancel(): void
    {
        $file = $this->dir . '/' . $this->fileName;

        if (is_file($file)) {
            @unlink($file);
        }
    }

    public function dir(string $type): string
    {
        return public_path(static::BASE_PATH . '/' . $type . 's');
    }

    public function fileName(string $id): string
    {
        return str_replace(':', '_', $id) . '.png';
    }

    public static function getPathToAssetImg(Product $product): string
    {
        $type = is_null($product->getItem()) ? 'packet' : $product->getItem()->getType()->getId();
        $fileName = is_null($product->getItem()) ? $product->getId() : $product->getItem()->getDataId();

        $path = "/uploads/shop/images/{$type}s/{$fileName}.png";

        return asset(str_replace(':', '_', $path));
    }
}