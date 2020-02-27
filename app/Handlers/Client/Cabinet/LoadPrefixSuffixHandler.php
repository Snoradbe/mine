<?php


namespace App\Handlers\Client\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Cabinet\CabinetUtils;
use App\Services\Cabinet\Prefix\PrefixSuffix;

class LoadPrefixSuffixHandler
{
    private $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $user, int $serverId): PrefixSuffix
    {
        $server = $this->getServer($serverId);

        $manager = CabinetUtils::getPermissionsManager($server);

        [$prefix, $suffix] = $manager->getPrefixSuffix($user->getUuid());

        if (empty($prefix) || empty($suffix)) {
            return PrefixSuffix::createEmpty();
        }

        return PrefixSuffix::createFromPermission($prefix, $suffix);
    }
}