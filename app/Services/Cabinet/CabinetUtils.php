<?php


namespace App\Services\Cabinet;


use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Services\Game\Permissions\PermissionsManager;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

final class CabinetUtils
{
    private function __construct() {}

    private static $defaultGroup;

    private static $managersCache = [];

    public static function getPermissionsManager(Server $server): PermissionsManager
    {
        if (isset(static::$managersCache[$server->getId()])) {
            return static::$managersCache[$server->getId()];
        }

        $config = config(
            'site.game.permissions.' . $server->getId(),
            config('site.game.permissions.default')
        );

        static::$managersCache[$server->getId()] = new $config['manager']($config, $server->getConnectionName());

        return static::$managersCache[$server->getId()];
    }

    public static function hasPermission(User $user, ?Server $server, string $permission): bool
    {
        $defaultPermissions = CabinetSettings::getDefaultPermissions();
        if (in_array($permission, $defaultPermissions)) {
            return true;
        }

        /* @var \App\Entity\Site\UserGroup $group */
        foreach ($user->getGroups() as $group)
        {
            if ((is_null($server) || $group->getServer() === $server) && $group->getGroup()->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public static function getDefaultGroup(): Group
    {
        if (!is_null(static::$defaultGroup)) {
            return static::$defaultGroup;
        }

        $group = new Group('default', 0, true, false, null);
        $permissions = CabinetSettings::getDefaultPermissions();

        $group->setPermissions($permissions);

        static::$defaultGroup = $group;

        return $group;
    }

    public static function saveDefaultGroup(Group $group): void
    {
        /* @var Settings $settings */
        $settings = app()->make(Settings::class);

        $cabinetSettings = settings('cabinet', DataType::JSON, []);
        $cabinetSettings['default_permissions'] = $group->getPermissions();

        $settings->set('cabinet', $cabinetSettings);
        $settings->save();
    }

    public static function hasSkinHead(string $username): bool
    {
        return is_file(config('site.skin_cloak.path', '') . "/skins/heads/$username.png");
    }
}