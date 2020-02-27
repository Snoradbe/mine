<?php


namespace App\Handlers\Admin\Vauchers;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Events\Admin\Vauchers\DeleteVaucherEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Vaucher\VaucherRepository;

class DeleteHandler
{
    private $vaucherRepository;

    public function __construct(VaucherRepository $vaucherRepository)
    {
        $this->vaucherRepository = $vaucherRepository;
    }

    private function getVaucher(int $id): Vaucher
    {
        $vaucher = $this->vaucherRepository->find($id);
        if (is_null($vaucher)) {
            throw new Exception('Ваучер не найден!');
        }

        return $vaucher;
    }

    public function handle(User $admin, int $id): void
    {
        $vaucher = $this->getVaucher($id);

        $this->vaucherRepository->delete($vaucher);

        event(new DeleteVaucherEvent($admin, $vaucher));
    }
}