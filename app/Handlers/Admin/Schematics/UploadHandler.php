<?php


namespace App\Handlers\Admin\Schematics;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\Schematics\UploadSchematicEvent;
use App\Exceptions\Exception;
use App\Exceptions\PermissionDeniedException;
use App\Helpers\StrHelper;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Game\Rcon\Connector;
use App\Services\Permissions\Permissions;
use Illuminate\Http\UploadedFile;

class UploadHandler
{
    private const SAVE_PATH = 'uploads/schematics';

    private $serverRepository;

    private $connector;

    public function __construct(ServerRepository $serverRepository, Connector $connector)
    {
        $this->serverRepository = $serverRepository;
        $this->connector = $connector;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $admin, UploadedFile $file, int $serverId): string
    {
        if ($file->getClientOriginalExtension() != 'schematic') {
            throw new Exception('Файл должен быть .schematic');
        }

        $server = $this->getServer($serverId);

        if (
            !$admin->permissions()->hasMPPermission(Permissions::MP_SCHEMATICS_UPLOAD_ALL)
            &&
            !is_null($admin->permissions()->getServersWithPermission(Permissions::MP_SCHEMATICS_UPLOAD))
            &&
            !in_array($server, $admin->permissions()->getServersWithPermission(Permissions::MP_SCHEMATICS_UPLOAD))
        ) {
            throw new PermissionDeniedException();
        }
		
		$size = $file->getSize();

        $name = $this->upload($file);

        $this->connector->connect($server->getIp(), $server->getRconPort(), $server->getRconPassword())
            ->send('schemload ' . $name);

        event(new UploadSchematicEvent($admin, $server, $name, $size));

        return $name;
    }

    private function upload(UploadedFile $file): string
    {
        $name = $this->filterName(mb_substr($file->getClientOriginalName(), 0, -strlen('.schematic')));

        $file->move(public_path(static::SAVE_PATH), $name . '.schematic');

        return $name;
    }

    private function filterName(string $name): string
    {
        return preg_replace('/[^a-zа-яё0-9]+/u', '_', StrHelper::transformRussian($name));
    }
}