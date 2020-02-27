<?php


namespace App\Services\Cabinet\Prefix;


use App\Entity\Site\Server;
use App\Entity\Site\User;

class PrefixChanger
{
    private function getConfig(Server $server): array
    {
        return config(
            'site.game.permissions.' . $server->getId(),
            config('site.game.permissions.default')
        );
    }

    private function setPrefix(Server $server, string $uuid, string $prefix): void
    {
        $config = $this->getConfig($server);

        /* @var \App\Services\Game\Permissions\PermissionsManager $manager */
        $manager = new $config['manager']($config, $server->getConnectionName());
        $manager->setPrefix($uuid, $prefix);
    }

    private function setSuffix(Server $server, string $uuid, string $suffix): void
    {
        $config = $this->getConfig($server);

        /* @var \App\Services\Game\Permissions\PermissionsManager $manager */
        $manager = new $config['manager']($config, $server->getConnectionName());
        $manager->setSuffix($uuid, $suffix);
    }

    public function change(User $user, Server $server, PrefixSuffix $prefix)
    {
        $this->setPrefix($server, $user->getUuid(), $prefix->prefixToPermissionFormat());
        $this->setSuffix($server, $user->getUuid(), $prefix->suffixToPermissionFormat());
    }
}