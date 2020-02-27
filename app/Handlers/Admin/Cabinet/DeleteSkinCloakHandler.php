<?php


namespace App\Handlers\Admin\Cabinet;


use App\Entity\Site\User;
use App\Events\Admin\Cabinet\DeleteSkinCloakEvent;
use App\Exceptions\Exception;
use App\Repository\Site\User\UserRepository;
use Illuminate\Filesystem\Filesystem;

class DeleteSkinCloakHandler
{
    private $userRepository;

    private $fileSystem;

    public function __construct(UserRepository $userRepository, Filesystem $fileSystem)
    {
        $this->userRepository = $userRepository;
        $this->fileSystem = $fileSystem;
    }

    private function getUser(int $id): User
    {
        $user = $this->userRepository->find($id);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    public function handle(User $admin, int $userId, string $type): void
    {
        $target = $this->getUser($userId);

        if ($type == 'skin') {
            $path = \App\Services\Cabinet\Skin\Image::getAbsolutePath($target->getName());
            if (!is_file($path)) {
                throw new Exception('Игрок еще не загружал скин!');
            }
        } else {
            $path = \App\Services\Cabinet\Cloak\Image::getAbsolutePath($target->getName());
            if (!is_file($path)) {
                throw new Exception('Игрок еще не загружал плащ!');
            }
        }

        $this->fileSystem->delete($path);

        event(new DeleteSkinCloakEvent($admin, $target, $type));

        if ($type == 'skin') {
            \App\Services\Cabinet\Skin\Image::deleteSkinHead($target->getName());
        }
    }
}