<?php

use App\Http\Middleware\Auth;
use App\Services\Permissions\Permissions;
use Illuminate\Routing\Router;

/**
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 * @var \Illuminate\Routing\Router $router;
 */

$baseGroup = [
    'middleware' => 'auth:' . Auth::IN_TEAM
];

if (!config('test.is_test', false)) {
    $baseGroup['domain'] = 'moder.realmine.net';
}

$router->group($baseGroup, function (Router $router) {

    $router->get('/', 'IndexController@render')->name('admin');

    $router->group(['prefix' => '/settings', 'namespace' => 'Settings', 'middleware' => permissions_middleware([Permissions::ALL, Permissions::MP_APPLICATIONS_FORMS_SERVER], true)], function (Router $router) {

        $router->group(['middleware' => permission_middleware(Permissions::ALL)], function (Router $router) {

            $router->group(['prefix' => '/groups'], function (Router $router) {

                $router->get('/', 'GroupsController@render')->name('admin.settings.groups');

                $router->post('/add/group/{server}', 'GroupsController@addGroup')
                    ->where('server', '[0-9]+');
                $router->post('/remove/group/{server}', 'GroupsController@removeGroup')
                    ->where('server', '[0-9]+');

                $router->post('/add/period/{server}/{group}', 'GroupsController@addPeriod')
                    ->where('server', '[0-9]+')
                    ->where('group', '[a-z0-9_]+');
                $router->post('/remove/period/{server}/{group}', 'GroupsController@removePeriod')
                    ->where('server', '[0-9]+')
                    ->where('group', '[a-z0-9_]+');

                $router->get('/prefix', 'PrefixController@render')->name('admin.settings.prefix');
                $router->post('/prefix', 'PrefixController@save');

                $router->get('/game-money', 'GameMoneyController@render')->name('admin.settings.game-money');
                $router->post('/game-money', 'GameMoneyController@save');

                $router->get('/skin-cloak', 'SkinCloakController@render')->name('admin.settings.skin-cloak');
                $router->post('/skin-cloak', 'SkinCloakController@save');

            });

            $router->group(['prefix' => '/tops'], function (Router $router) {

                $router->get('/', 'TopsController@render')->name('admin.settings.tops');
                $router->post('/', 'TopsController@save');

            });

            $router->get('/unban', 'UnbanController@render')->name('admin.settings.unban');
            $router->post('/unban', 'UnbanController@save');

            $router->get('/referal', 'ReferalController@render')->name('admin.settings.referal');
            $router->post('/referal', 'ReferalController@save');

        });

        $router->group(['prefix' => '/applications', 'middleware' => permission_middleware(Permissions::MP_APPLICATIONS_FORMS_SERVER)], function (Router $router) { //+

            $router->get('/', 'ApplicationsController@render')->name('admin.settings.applications')->middleware(permission_middleware(Permissions::MP_APPLICATIONS, true)); //+

            $router->post('/edit-group.self', 'ApplicationsController@editGroupSelf')->name('admin.settings.applications.edit-group.self'); //+
            $router->post('/edit-server-form.self', 'ApplicationsController@editServerSelfForm')->name('admin.settings.applications.edit-server-form.self'); //+

            $router->group(['middleware' => permission_middleware(Permissions::MP_APPLICATIONS_ALL)], function (Router $router) { //+

                $router->post('/add-group', 'ApplicationsController@addGroup')->name('admin.settings.applications.add-group'); //+
                $router->post('/edit-group', 'ApplicationsController@editGroup')->name('admin.settings.applications.edit-group'); //+
                $router->post('/delete-group', 'ApplicationsController@deleteGroup')->name('admin.settings.applications.delete-group'); //+

                $router->post('/edit-form', 'ApplicationsController@editForm')->name('admin.settings.applications.edit-form'); //+
                $router->post('/edit-descr', 'ApplicationsController@editDescription')->name('admin.settings.applications.edit-descr'); //+
                $router->post('/edit-rules', 'ApplicationsController@editRules')->name('admin.settings.applications.edit-rules'); //+
                $router->post('/edit-server-form', 'ApplicationsController@editServerForm')->name('admin.settings.applications.edit-server-form'); //+
                $router->post('/edit-cooldown', 'ApplicationsController@editCooldown')->name('admin.settings.applications.edit-cooldown'); //+
                $router->post('/edit-min-level', 'ApplicationsController@editMinLevel')->name('admin.settings.applications.edit-min-level'); //+

            });

        });

    });

    $router->group(['prefix' => '/servers', 'namespace' => 'Servers', 'middleware' => permission_middleware(Permissions::ALL, true)], function (Router $router) {

        $router->get('/', 'ListController@render')->name('admin.servers');
        $router->post('/', 'ListController@add');
        $router->post('/delete/{id}', 'ListController@delete')->name('admin.servers.delete')
            ->where('id', '[0-9]+');

        $router->get('/{id}', 'EditController@render')->name('admin.servers.edit')
            ->where('id', '[0-9]+');
        $router->post('/{id}', 'EditController@edit')
            ->where('id', '[0-9]+');

    });

    $router->group(['prefix' => '/groups', 'namespace' => 'Groups', 'middleware' => permission_middleware(Permissions::ALL, true)], function (Router $router) {

        $router->get('/', 'ListController@render')->name('admin.groups');
        $router->post('/', 'ListController@add');
        $router->post('/delete/{id}', 'ListController@delete')->name('admin.groups.delete')
            ->where('id', '[0-9]+');

        $router->get('/{id}', 'EditController@render')->name('admin.groups.edit')
            ->where('id', '[0-9]+');
        $router->post('/{id}', 'EditController@edit')
            ->where('id', '[0-9]+');

    });

    $router->group(['prefix' => '/team', 'namespace' => 'Team', 'middleware' => permission_middleware(Permissions::MP_TEAM, true, true)], function (Router $router) { //+

        $router->get('/', 'ListController@render')->name('admin.team'); //+
        $router->post('/add', 'ListController@add')->name('admin.team.add')
            ->middleware(permission_middleware(Permissions::MP_TEAM_ADD)); //+

        $router->post('/transit', 'ListController@transit')->name('admin.team.transit')
            ->middleware(permission_middleware(Permissions::MP_TEAM_TRANSIT)); //+

        $router->post('/update', 'ListController@update')->name('admin.team.update')
            ->middleware(permission_middleware(Permissions::MP_TEAM_UPGRADE)); //+

        $router->post('/delete', 'ListController@delete')->name('admin.team.delete')
            ->middleware(permission_middleware(Permissions::MP_TEAM_REMOVE)); //+

    });

    $router->group(['prefix' => '/donaters', 'namespace' => 'Donaters', 'middleware' => permission_middleware(Permissions::MP_DONATERS_VIEW, true)], function (Router $router) {

        $router->get('/', 'ListController@render')->name('admin.donaters');

    });

    $router->group(['prefix' => '/banlist', 'namespace' => 'Banlist', 'middleware' => permission_middleware(Permissions::MP_BANLIST, true, true)], function (Router $router) { //+

        $router->get('/bans', 'BansController@list')->name('admin.banlist'); //+

        $router->post('/ban', 'BansController@ban')->name('admin.banlist.ban')
            ->middleware(permission_middleware(Permissions::MP_BANLIST_BAN, true)); //+

        $router->post('/unban', 'BansController@unban')->name('admin.banlist.unban')
            ->middleware(permission_middleware(Permissions::MP_BANLIST_UNBAN, true)); //+

    });

    $router->group(['prefix' => '/applications', 'namespace' => 'Applications', 'middleware' => permission_middleware(Permissions::MP_APPLICATIONS_VIEW , true)], function (Router $router) {

        $router->get('/{type?}', 'ApplicationsController@render')->name('admin.applications')
            ->where('type', '[0-9]+'); //+
        $router->post('/{type?}', 'ApplicationsController@manage')
            ->where('type', '[0-9]+'); //+

    });

    $router->group(['prefix' => '/vauchers', 'namespace' => 'Vauchers', 'middleware' => permission_middleware(Permissions::MP_VAUCHERS_VIEW, true)], function (Router $router) { //+

        $router->get('/', 'VauchersController@render')->name('admin.vauchers'); //+
        $router->post('/', 'VauchersController@add')
            ->middleware(permission_middleware(Permissions::MP_ALL));

        $router->get('/edit/{id}', 'EditController@render')->name('admin.vauchers.edit')
            ->where('id', '[0-9]+')->middleware(permission_middleware(Permissions::MP_ALL));
        $router->post('/edit/{id}', 'EditController@edit')
            ->where('id', '[0-9]+')->middleware(permission_middleware(Permissions::MP_ALL));

        $router->post('/delete/{id}', 'VauchersController@delete')->name('admin.vauchers.delete')
            ->where('id', '[0-9]+')->middleware(permission_middleware(Permissions::MP_ALL));

    });

    $router->get('/screenshoter', 'ScreenShoter\ScreenShoterController@render')->name('admin.screenshoter')
        ->middleware(permission_middleware(Permissions::MP_SCREENSHOTER_ALL)); //+

    $router->post('/screenshoter/load', 'ScreenShoter\ScreenShoterController@loadDate')->name('admin.screenshoter.load')
        ->middleware(permission_middleware(Permissions::MP_SCREENSHOTER_ALL)); //+

    $router->group(['prefix' => '/server-permissions', 'namespace' => 'ServerPermissions', 'middleware' => permission_middleware(Permissions::ALL, true)], function (Router $router) {

        $router->get('/', 'ServerPermissionsController@render')->name('admin.server_perms');

        $router->group(['prefix' => '/{server}', 'where' => ['server' => '[0-9]+']], function (Router $router) {

            $router->get('/', 'ServerPermissionsController@renderServer')->name('admin.server_perms.list');

            $router->post('/add', 'ServerPermissionsController@add')->name('admin.server_perms.add');
            $router->post('/delete', 'ServerPermissionsController@delete')->name('admin.server_perms.delete');

        });

    });

    $router->group(['prefix' => '/admin-perms', 'namespace' => 'AdminPerms', 'middleware' => permission_middleware(Permissions::ALL, true)], function (Router $router) {

        $router->get('/', 'AdminPermsController@render')->name('admin.admin_perms');

        $router->post('/add', 'AdminPermsController@add')->name('admin.admin_perms.add');
        $router->post('/delete', 'AdminPermsController@delete')->name('admin.admin_perms.delete');

    });

    $router->group(['prefix' => '/site-perms', 'namespace' => 'SitePerms', 'middleware' => permission_middleware(Permissions::ALL, true)], function (Router $router) {

        $router->get('/', 'SitePermsController@render')->name('admin.site_perms');

        $router->post('/add', 'SitePermsController@add')->name('admin.site_perms.add');
        $router->post('/delete', 'SitePermsController@delete')->name('admin.site_perms.delete');

    });

    $router->group(['prefix' => '/schematics', 'namespace' => 'Schematics', 'middleware' => permission_middleware(Permissions::MP_SCHEMATICS, true, true)], function (Router $router) { //+

       $router->get('/', 'SchematicsController@render')->name('admin.schematics'); //+

       $router->post('/upload', 'SchematicsController@upload')->name('admin.schematics.upload'); //+

    });

    $router->group(['prefix' => '/logs', 'namespace' => 'Logs'], function (Router $router) {

        $router->get('/server', 'ServerLogsController@render')->name('admin.logs.server')
            ->middleware(permission_middleware(Permissions::MP_LOGS_SERVER, true)); //+
        $router->post('/server/get/{server}', 'ServerLogsController@getLogs')
            ->where('server', '[0-9]+')
            ->middleware(permission_middleware(Permissions::MP_LOGS_SERVER, true)); //+


        $router->get('/shop', 'ShopLogsController@render')->name('admin.logs.shop')
            ->middleware(permission_middleware(Permissions::MP_LOGS_SHOP, true)); //+
        $router->post('/shop/get/{server}', 'ShopLogsController@getLogs')
            ->where('server', '[0-9]+')
            ->middleware(permission_middleware(Permissions::MP_LOGS_SHOP, true)); //+


        $router->get('/cabinet', 'CabinetLogsController@render')->name('admin.logs.cabinet')
            ->middleware(permission_middleware(Permissions::MP_LOGS_CABINET, true)); //+
        $router->post('/cabinet/get/{server}', 'CabinetLogsController@getLogs')
            ->where('server', '[0-9]+')
            ->middleware(permission_middleware(Permissions::MP_LOGS_CABINET, true)); //+


        $router->get('/admin', 'AdminLogsController@render')->name('admin.logs.admin')
            ->middleware(permission_middleware(Permissions::MP_ALL, true));
        $router->post('/admin/get/{server?}', 'AdminLogsController@getLogs')
            ->where('server', '[0-9]+')
            ->middleware(permission_middleware(Permissions::MP_ALL, true));

    });

    //SHOP
    $router->group(['prefix' => '/shop', 'namespace' => 'Shop', 'middleware' => permission_middleware(Permissions::MP_SHOP_MANAGE, true)], function (\Illuminate\Routing\Router $router) { //+

        $router->get('/', 'ShopController@render')->name('admin.shop');

        $router->group(['prefix' => '/item', 'namespace' => 'Item'], function (\Illuminate\Routing\Router $router) {

            $router->get('/list', 'ListController@render')->name('admin.shop.item_list');

            $router->get('/add', 'AddController@render')->name('admin.shop.item_add');
            $router->post('/add', 'AddController@add');

            $router->get('/edit/{id}', 'EditController@render')->name('admin.shop.item_edit')
                ->where('id', '[0-9]+');
            $router->post('/edit/{id}', 'EditController@edit')
                ->where('id', '[0-9]+');

        });

        $router->group(['prefix' => '/product', 'namespace' => 'Product'], function (\Illuminate\Routing\Router $router) {

            $router->get('/list', 'ListController@render')->name('admin.shop.product_list');
            $router->get('/load/{page}', 'ListController@loadProducts')
                ->where('page', '[0-9]+');

            $router->get('/add', 'AddController@render')->name('admin.shop.product_add');
            $router->post('/add/one', 'AddController@addOne');
            $router->post('/add/packet', 'AddController@addPacket');

            $router->post('/enable/{id}', 'EditController@enable')
                ->where('id', '[0-9]+');

            $router->get('/edit/{id}', 'EditController@render')->name('admin.shop.product_edit')
                ->where('id', '[0-9]+');
            $router->post('/edit/one/{id}', 'EditController@editOne')
                ->where('id', '[0-9]+');
            $router->post('/edit/packet/{id}', 'EditController@editPacket')
                ->where('id', '[0-9]+');

        });

        $router->group(['prefix' => '/category', 'namespace' => 'Category'], function (\Illuminate\Routing\Router $router) {

            $router->get('/list', 'ListController@render')->name('admin.shop.category_list');
            $router->get('/load/{page}', 'ListController@loadProducts')
                ->where('page', '[0-9]+');

            $router->get('/add', 'AddController@render')->name('admin.shop.category_add');
            $router->post('/add', 'AddController@add');

            $router->post('/enable/{id}', 'EditController@enable')
                ->where('id', '[0-9]+');

            $router->get('/edit/{id}', 'EditController@render')->name('admin.shop.category_edit')
                ->where('id', '[0-9]+');
            $router->post('/edit/{id}', 'EditController@edit')
                ->where('id', '[0-9]+');

        });

        $router->group(['prefix' => '/discounts'], function (\Illuminate\Routing\Router $router) {

            $router->get('/', 'DiscountsController@render')->name('admin.shop.discounts');

            $router->get('/load-products', 'DiscountsController@loadDiscountProducts');
            $router->post('/random', 'DiscountsController@random');
            $router->post('/set/{id}', 'DiscountsController@setDiscount')
                ->where('id', '[0-9]+');

        });

        $router->get('/statistics', 'StatisticsController@render')->name('admin.shop.statistics');

        $router->group(['prefix' => '/player'], function (\Illuminate\Routing\Router $router) {

            $router->get('/warehouse', 'PlayerController@warehouse')->name('admin.shop.player.warehouse');
            $router->post('/get-warehouse', 'PlayerController@getWarehouse');

            $router->post('/give-product/{id}', 'PlayerController@giveProduct')
                ->where('id', '[0-9]+');

            $router->post('/remove-purchase/{id}', 'PlayerController@cancelPurchase')
                ->where('id', '[0-9]+');

        });

    });

    $router->group(['prefix' => '/discounts', 'namespace' => 'Discounts', 'middleware' => permission_middleware(Permissions::ALL, true)], function (Router $router) {

        $router->get('/', 'DiscountsController@render')->name('admin.discounts');
        $router->post('/add', 'DiscountsController@add')->name('admin.discounts.add');
        $router->post('/delete', 'DiscountsController@delete')->name('admin.discounts.delete');

    });

    $router->group(['prefix' => '/cabinet', 'namespace' => 'Cabinet', 'middleware' => permission_middleware(Permissions::MP_CABINET_VIEW, true)], function (Router $router) {

        $router->get('/', 'CabinetController@render')->name('admin.cabinet');
        $router->get('/player', 'CabinetController@player')->name('admin.cabinet.player');

        $router->group(['prefix' => '/player/{id}', 'where' => ['id' => '[0-9]+']], function (Router $router) {

            $router->post('/delete-skin-cloak', 'CabinetController@deleteSkinCloak')->name('admin.cabinet.player.sc-delete')
                ->middleware(permission_middleware(Permissions::MP_CABINET_DELETE_SKIN_CLOAK, true));
            $router->post('/set-valute', 'CabinetController@setValute')->name('admin.cabinet.player.set-valute')
                ->middleware(permission_middleware(Permissions::MP_CABINET_SET_VALUTE, true));
            $router->post('/give-group', 'CabinetController@giveGroup')->name('admin.cabinet.player.give-group')
                ->middleware(permission_middleware(Permissions::MP_CABINET_GIVE_GROUP, true));
            $router->post('/remove-group', 'CabinetController@removeGroup')->name('admin.cabinet.player.remove-group')
                ->middleware(permission_middleware(Permissions::MP_CABINET_REMOVE_GROUP, true));
            $router->post('/change-prefix', 'CabinetController@changePrefix')->name('admin.cabinet.player.change-prefix')
                ->middleware(permission_middleware(Permissions::MP_CABINET_CHANGE_PREFIX, true));
            $router->post('/remove-prefix', 'CabinetController@removePrefix')->name('admin.cabinet.player.remove-prefix')
                ->middleware(permission_middleware(Permissions::MP_CABINET_REMOVE_PREFIX, true));

        });

    });

    $router->group(['prefix' => '/bugreports', 'namespace' => 'BugReport', 'middleware' => permission_middleware(Permissions::MP_BUGREPORT_ALL, true)], function (Router $router) { //+

        $router->get('/', 'BugReportController@render')->name('admin.bugreports'); //+
        $router->post('/status/{id}', 'BugReportController@changeStatus')->name('admin.bugreports.status')
            ->where('id', '[0-9]+'); //+

        $router->get('/{id}', 'MessageController@render')->name('admin.bugreports.read')
            ->where('id', '[0-9]+'); //+
        $router->post('/send/{id}', 'MessageController@send')->name('admin.bugreports.send-message')
            ->where('id', '[0-9]+'); //+

    });

    $router->group(['prefix' => '/hwid-bans', 'namespace' => 'HwidBans', 'middleware' => permission_middleware(Permissions::MP_HWID_BANS, true)], function (Router $router) {

        $router->get('/', 'HwidBansController@render')->name('admin.hwid_bans');
        $router->post('/ban', 'HwidBansController@ban')->name('admin.hwid_bans.ban');
        $router->post('/unban', 'HwidBansController@unban')->name('admin.hwid_bans.unban');

    });

});