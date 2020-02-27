<?php


namespace App\Handlers\Client\Cabinet;


use App\Entity\Site\User;
use App\Events\Client\Cabinet\SkinCloakUploadEvent;
use App\Exceptions\Exception;
use App\Services\Cabinet\Skin\Resolution;
use App\Services\Cabinet\Skin\Validator;
use App\Services\Permissions\Permissions;
use App\Services\Skills\Skills;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class UploadSkinHandler
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
        if ($user->permissions()->hasPermission(Permissions::CABINET_SKIN_HD) || Skills::hasHDSkinSkill($user)) {
            if (!$this->validator->validate($image->getWidth(), $image->getHeight())) {
                throw new Exception('Невалидные пропорции скина!');
            }

            if (!$this->resolution->isAny($image)) {
                throw new Exception('Превышен допустимый размер ширины или высоты скина!');
            }

            event(new SkinCloakUploadEvent($user, 'skin'));

            $this->move($image, $user);

            $this->saveHead($user);

            return;
        }

        if ($user->permissions()->hasPermission(Permissions::CABINET_SKIN_UPLOAD)) {
            if (!$this->validator->validate($image->getWidth(), $image->getHeight())) {
                throw new Exception('Невалидные пропорции скина!');
            }

            if (!$this->resolution->isSD($image)) {
                throw new Exception('Превышен допустимый размер ширины или высоты скина!');
            }

            event(new SkinCloakUploadEvent($user, 'skin'));

            $this->move($image, $user);

            $this->saveHead($user);

            return;
        }

        throw new Exception('Недостаточно прав для загрузки скина!');
    }

    private function move(Image $image, User $user): void
    {
        $image->save(\App\Services\Cabinet\Skin\Image::getAbsolutePath($user->getName()));
    }

    private function saveHead(User $user): void
    {
        try {
            \App\Services\Cabinet\Skin\Image::saveSkinHead($user->getName());
        } catch (Exception $exception) {
            //удалять скин если не получилось?
        }
    }
}