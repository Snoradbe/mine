<?php


namespace App\Services\Permissions;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Entity\Site\UserAdminGroup;
use App\Entity\Site\UserGroup;
use App\Services\Cabinet\CabinetUtils;

class Permissions
{
    public const

        ALL = '*', //Все права на все
        MP_ALL = 'mp.*', //Все права на модер-панель
        CABINET_ALL = 'cabinet.*', //Все права на кабинет

        CABINET_SKIN_CLOAK = 'cabinet.skin_cloak.*', //Все права на скины и плащи
        CABINET_SKIN_UPLOAD = 'cabinet.skin_cloak.skin.upload', //Загрузка скина
        CABINET_SKIN_HD = 'cabinet.skin_cloak.skin.*', //Загрузка HD скина
        CABINET_CLOAK_UPLOAD = 'cabinet.skin_cloak.cloak.upload', //Загрузка плаща
        CABINET_CLOAK_HD = 'cabinet.skin_cloak.cloak.*', //Загрузка HD плаща
        CABINET_PREFIX = 'cabinet.prefix', //Изменение префиксов

        MP_SHOP_MANAGE = 'shop.manage', //Управление магазином на сайте
        SHOP_NO_STATISTIC = 'shop.no_statistic', //Не записывать в статистику при покупке

        MP_APPLICATIONS = 'mp.applications',
        MP_APPLICATIONS_ALL = 'mp.applications.*', //Все права на заявки
        MP_APPLICATIONS_VIEW = 'mp.applications.view', //Просмотр заявок только на своем сервере
        MP_APPLICATIONS_VIEW_ALL = 'mp.applications.view.*', //Просмотр заявок на любом сервере
        MP_APPLICATIONS_MANAGE = 'mp.applications.manage', //Управление заявками своего сервера
        MP_APPLICATIONS_MANAGE_ALL = 'mp.applications.manage.*', //Управление заявками любого сервера

        MP_APPLICATIONS_FORMS = 'mp.applications_forms',
        MP_APPLICATIONS_FORMS_ALL = 'mp.applications_forms.*', //Все права на изменения форм заявок
        MP_APPLICATIONS_FORMS_SERVER = 'mp.applications_forms.server', //Изменение серверных вопросов и влючение/отключение набора

        MP_BANLIST = 'mp.banlist',
        MP_BANLIST_ALL = 'mp.banlist.*', //Все права на банлист
        MP_BANLIST_BAN = 'mp.banlist.ban', //Банить
        MP_BANLIST_UNBAN = 'mp.banlist.unban', //Разбанивать

        MP_DONATERS = 'mp.donaters',
        MP_DONATERS_ALL = 'mp.donaters.*', //Все права на списов донатеров
        MP_DONATERS_VIEW = 'mp.donaters.view', //Просмотр донатеров своего сервера
        MP_DONATERS_VIEW_ALL = 'mp.donaters.view.*', //Просмотр донатеров любого сервера

        MP_SCREENSHOTER = 'mp.screenshoter',
        MP_SCREENSHOTER_ALL = 'mp.screenshoter.*', //Все права на просмотр скринов

        MP_TEAM = 'mp.team',
        MP_TEAM_ALL = 'mp.team.*', //Все права на управление администрацией
        MP_TEAM_VIEW = 'mp.team.view', //Просмотр администрации своего сервера
        MP_TEAM_VIEW_ALL = 'mp.team.view.*', //Просмотр администрации любого сервера [аналог масс, тоесть сможет добавлять на любой серв, в том числе на масс!]
        MP_TEAM_ADD = 'mp.team.add', //Принятие на должность
        MP_TEAM_TRANSIT = 'mp.team.transit', //Перевод между серверами
        MP_TEAM_UPGRADE = 'mp.team.upgrade', //Изменение группы
        MP_TEAM_REMOVE = 'mp.team.remove', //Удаление группы
        MP_TEAM_ALL_GROUPS = 'mp.team.all_groups', //Выдача любых групп меньше своей

        MP_VAUCHERS = 'mp.vauchers',
        MP_VAUCHERS_VIEW = 'mp.vauchers.view', //Просмотр персональных ваучеров
        MP_VAUCHERS_VIEW_ALL = 'mp.vauchers.view.*', //Просмотр всех ваучеров

        MP_SCHEMATICS = 'mp.schematics',
        MP_SCHEMATICS_ALL = 'mp.schematics.*', //Все права на схематики
        MP_SCHEMATICS_UPLOAD = 'mp.schematics.upload', //Загрузка схематиков на свой сервер
        MP_SCHEMATICS_UPLOAD_ALL = 'mp.schematics.upload.*', //Загрузка схематиков на все сервера

        MP_LOGS = 'mp.logs',
        MP_LOGS_ALL = 'mp.logs.*', //Все права на логи
        MP_LOGS_SERVER = 'mp.logs.server', //Логи своего сервера
        MP_LOGS_SERVER_ALL = 'mp.logs.server.*', //Логи любого сервера
        MP_LOGS_SHOP = 'mp.logs.shop', //Логи магазина своего сервера
        MP_LOGS_SHOP_ALL = 'mp.logs.shop.*', //Логи магазина любого сервера
        MP_LOGS_CABINET = 'mp.logs.cabinet', //Логи кабинета своего сервера
        MP_LOGS_CABINET_ALL = 'mp.logs.cabinet.*', //Логи кабинета любого сервера

		MP_CABINET = 'mp.cabinet',
		MP_CABINET_ALL = 'mp.cabinet.*', //Все права на кабинет
		MP_CABINET_VIEW_ALL = 'mp.cabinet.view.*', //Просмотр кабинета любого сервера
		MP_CABINET_VIEW = 'mp.cabinet.view', //Просмотр кабинет своего сервера
		MP_CABINET_DELETE_SKIN_CLOAK = 'mp.cabinet.delete_skin_cloak', //Удаление скинов/плащей
		MP_CABINET_SET_VALUTE = 'mp.cabinet.set_valute', //Изменение валюты
		MP_CABINET_GIVE_GROUP = 'mp.cabinet.give_group', //Выдача группы
		MP_CABINET_REMOVE_GROUP = 'mp.cabinet.remove_group', //Удаление группы
		MP_CABINET_CHANGE_PREFIX = 'mp.cabinet.change_prefix', //Изменение префиксов
		MP_CABINET_REMOVE_PREFIX = 'mp.cabinet.remove_prefix', //Удаление префиксов

        MP_BUGREPORT_ALL = 'mp.bugreport.*', //Управление баг-репортами

        MP_HWID_BANS = 'mp.hwid_bans.*' //Управление банами по железу

    ;

    protected const NOT_IN_CACHE = -1;
    
    private $user;
    
    private $cache = [];
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    private function cacheKey(string $permission, ?Server $server): string
    {
        return $permission . $server;
    }
    
    private function checkCache(string $permission, ?Server $server)
    {
        $key = $this->cacheKey($permission, $server);

        return $this->cache[$key] ?? static::NOT_IN_CACHE;
    }
    
    private function saveResult(string $permission, ?Server $server, bool $result): bool
    {
        $key = $this->cacheKey($permission, $server);

        $this->cache[$key] = $result;

        return $result;
    }

    /**
     * Проверка хотябы одного пермишена из списка
     * Подходит для проверки прав групповой менюшки
     *
     * @param array $permissions
     * @param Server|null $server
     * @return bool
     */
    public function hasMPAnyPermissions(array $permissions, ?Server $server = null): bool
    {
        foreach ($permissions as $permission)
        {
            if ($this->hasMPPermission($permission, $server)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверка админских пермишенов
     *
     * @param string $permission
     * @param Server|null $server
     * @return bool
     */
    public function hasMPPermission(string $permission, ?Server $server = null): bool
    {
        $cacheStatus = $this->checkCache($permission, $server);
        if ($cacheStatus !== static::NOT_IN_CACHE) {
            return $cacheStatus;
        }

        /**
         * @var UserAdminGroup $userAdminGroup
         */
        foreach ($this->user->getAdminGroups() as $userAdminGroup)
        {
            if (!is_null($server) && !is_null($userAdminGroup->getServer()) && $server !== $userAdminGroup->getServer()) {
                continue;
            }

            if ($userAdminGroup->getGroup()->hasPermission($permission)) {
                return $this->saveResult($permission, $server, true);
            }
        }

        return $this->saveResult($permission, $server, false);
    }

    /**
     * Проверка сайтовых пермишенов
     *
     * @param string $permission
     * @param Server|null $server
     * @return bool
     */
    public function hasPermission(string $permission, ?Server $server = null): bool
    {
        $cacheStatus = $this->checkCache($permission, $server);
        if ($cacheStatus !== static::NOT_IN_CACHE) {
            return $cacheStatus;
        }

        /**
         * @var UserGroup $userGroup
         */
        foreach ($this->user->getGroups() as $userGroup)
        {
            if (!is_null($server) && $server !== $userGroup->getServer()) {
                continue;
            }

            if ($userGroup->getGroup()->hasPermission($permission)) {
                return $this->saveResult($permission, $server, true);
            }
        }

        $defaultGroup = CabinetUtils::getDefaultGroup();
        if ($defaultGroup->hasPermission($permission)) {
            return $this->saveResult($permission, $server, true);
        }

        return $this->hasMPPermission($permission, $server);
    }

    /**
     * Проверка у админских групп наличие любого пермишена с префиксом
     *
     * @param string $prefix
     * @param Server|null $server
     * @return bool
     */
    public function containsMPPermissionPrefix(string $prefix, ?Server $server = null): bool
    {
        $cacheStatus = $this->checkCache($prefix, $server);
        if ($cacheStatus !== static::NOT_IN_CACHE) {
            return $cacheStatus;
        }

        /**
         * @var UserAdminGroup $userAdminGroup
         */
        foreach ($this->user->getAdminGroups() as $userAdminGroup)
        {
            if (!is_null($server) && !is_null($userAdminGroup->getServer()) && $server !== $userAdminGroup->getServer()) {
                continue;
            }

            if ($userAdminGroup->getGroup()->containsPermissionPrefix($prefix)) {
                return $this->saveResult($prefix, $server, true);
            }
        }

        return $this->saveResult($prefix, $server, false);
    }

    /**
     * Проверка у сайтовых групп наличие любого пермишена с префиксом
     *
     * @param string $prefix
     * @param Server|null $server
     * @return bool
     */
    public function containsPermissionPrefix(string $prefix, ?Server $server = null): bool
    {
        $cacheStatus = $this->checkCache($prefix, $server);
        if ($cacheStatus !== static::NOT_IN_CACHE) {
            return $cacheStatus;
        }

        /**
         * @var UserGroup $userGroup
         */
        foreach ($this->user->getGroups() as $userGroup)
        {
            if (!is_null($server) && $server !== $userGroup->getServer()) {
                continue;
            }

            if ($userGroup->getGroup()->containsPermissionPrefix($prefix)) {
                return $this->saveResult($prefix, $server, true);
            }
        }

        $defaultGroup = CabinetUtils::getDefaultGroup();
        if ($defaultGroup->containsPermissionPrefix($prefix)) {
            return $this->saveResult($prefix, $server, true);
        }

        return $this->containsMPPermissionPrefix($prefix, $server);
    }

    /**
     * Поиск серверов по пермишену
     * Если null, то значит права на всех серверах
     *
     * @param string $permission
     * @return Server[]|null
     */
    public function getServersWithPermission(string $permission): ?array
    {
        $servers = [];

        /**
         * @var UserAdminGroup $adminGroup
         */
        foreach ($this->user->getAdminGroups() as $adminGroup)
        {
            if ($adminGroup->getGroup()->hasPermission($permission)) {
                if (is_null($adminGroup->getServer())) {
                    return null;
                }

                if (!in_array($adminGroup->getServer(), $servers)) {
                    $servers[] = $adminGroup->getServer();
                }
            }
        }

        return $servers;
    }
}