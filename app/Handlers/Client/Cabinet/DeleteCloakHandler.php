<?php


namespace App\Handlers\Client\Cabinet;


use App\Entity\Site\User;
use App\Events\Client\Cabinet\SkinCloakDeleteEvent;
use App\Exceptions\Exception;
use App\Services\Cabinet\Cloak\Image;
use Illuminate\Filesystem\Filesystem;

class DeleteCloakHandler
{
    private $fileSystem;

    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function handle(User $user): void
    {
        $path = Image::getAbsolutePath($user->getName());
        if (!is_file($path)) {
            throw new Exception('Вы еще не загружали плащ!');
        }

        $this->fileSystem->delete($path);

        event(new SkinCloakDeleteEvent($user, 'cloak'));
    }
}