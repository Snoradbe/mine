<?php


namespace App\Handlers\Client\Cabinet;


use App\Entity\Site\User;
use App\Events\Client\Cabinet\SkinCloakUploadEvent;
use App\Exceptions\Exception;
use App\Services\Cabinet\Cloak\Resolution;
use App\Services\Cabinet\Cloak\Validator;
use App\Services\Permissions\Permissions;
use App\Services\Skills\Skills;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class UploadCloakHandler
{
    private $imageManager;

    private $validator;

    private $resolution;

    public function __construct(ImageManager $imageManager, Validator $validator, Resolution $resolution)
    {
        $this->imageManager = $imageManager;

        $this->validator = $validator;

        $this->resolution = $resolution;
    }

    public function handle(User $user, UploadedFile $file)
    {
        $image = $this->imageManager->make($file);
        if ($user->permissions()->hasPermission(Permissions::CABINET_CLOAK_HD) || Skills::hasHDCloakSkill($user)) {
            if (!$this->validator->validate($image->getWidth(), $image->getHeight())) {
                throw new Exception('Невалидные пропорции плаща!');
            }

            if (!$this->resolution->isAny($image)) {
                throw new Exception('Превышен допустимый размер ширины или высоты плаща!');
            }

            $this->move($image, $user);

            event(new SkinCloakUploadEvent($user, 'cloak'));

            return;
        }

        if ($user->permissions()->hasPermission(Permissions::CABINET_CLOAK_UPLOAD) || Skills::hasCloakSkill($user)) {
            if (!$this->validator->validate($image->getWidth(), $image->getHeight())) {
                throw new Exception('Невалидные пропорции плаща!');
            }

            if (!$this->resolution->isSD($image)) {
                throw new Exception('Превышен допустимый размер ширины или высоты плаща!');
            }

            $this->move($image, $user);

            event(new SkinCloakUploadEvent($user, 'cloak'));

            return;
        }

        throw new Exception('Недостаточно прав для загрузки плаща!');
    }

    private function move(Image $image, User $user): void
    {
        $image->save(\App\Services\Cabinet\Cloak\Image::getAbsolutePath($user->getName()));
    }
}