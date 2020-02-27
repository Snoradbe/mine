<?php

use Illuminate\Routing\Router;

/**
 * Для CSRF проверки, нужно добавить middleware laratoken (только после auth:logged!)
 *
 * @var Router $router
 */

$router->post('/test1', function () {

    return csrf_token();

})->middleware('auth:' . \App\Http\Middleware\Auth::LOGGED, 'laratoken');

$router->get('/skin/head/{user}', function(string $user) {

    $basePath = config('site.skin_cloak.path', '');
    $hasHead = \App\Services\Cabinet\CabinetUtils::hasSkinHead($user);
    if ($hasHead) {
        $path = $basePath . "/skins/heads/{$user}.png";
    } else {
        $path = $basePath . "/skins/heads/default.png";
    }

    header('Content-Type: image/png');

    readfile($path);
    //print file_get_contents($path);

})->where('user', '[A-Za-z0-9\_\-]+');


$router->group(['middleware' => 'auth:' . \App\Http\Middleware\Auth::LOGGED], function (Router $router) {

    $router->group(['middleware' => 'laratoken'], function (Router $router) {

        $router->post('/load', 'LoadController@load');

        // КАБИНЕТ
        $router->group(['prefix' => '/cabinet', 'namespace' => 'Cabinet'], function (Router $router) {

            $router->post('/load', 'CabinetController@load');

            $router->post('/skin/upload', 'SkinCloakController@uploadSkin');
            $router->post('/skin/delete', 'SkinCloakController@deleteSkin');

            $router->post('/cloak/upload', 'SkinCloakController@uploadCloak');
            $router->post('/cloak/delete', 'SkinCloakController@deleteCloak');

            $router->group(['prefix' => '/{server}', 'where' => ['server' => '[0-9]+']], function (Router $router) {

                $router->post('/buy-group', 'GroupsController@buy');

                $router->post('/get-prefix', 'PrefixController@getPrefix');
                $router->post('/set-prefix', 'PrefixController@setPrefix');

                $router->post('/buy-cases', 'CasesController@buy');

            });

        });

        //МАГАЗИН
        $router->group(['prefix' => '/shop', 'namespace' => 'Shop'], function (Router $router) {

            $router->group(['prefix' => '/{server}', 'where' => ['server' => '[0-9]+']], function (Router $router) {

                $router->post('/load', 'ShopController@load');
                $router->post('/load-products/{page}', 'ShopController@loadProducts')
                    ->where('page', '[0-9]+');
                $router->post('/buy', 'ShopController@buy');

                $router->post('/load-warehouse/{page}', 'WarehouseController@load')
                    ->where('page', '[0-9]+');

            });

            $router->post('/cancel/{id}', 'WarehouseController@cancelPurchase')
                ->where('id', '[0-9]+');

        });

        //ЗАЯВКИ
        $router->group(['prefix' => '/applications', 'namespace' => 'Applications'], function (Router $router) {

            $router->post('/load', 'ApplicationsController@load');

            $router->group(['prefix' => '/{server}', 'where' => ['server' => '[0-9]+']], function (Router $router) {

                $router->post('/load-form', 'ApplicationsController@loadForm');
                $router->post('/send', 'ApplicationsController@send');

            });

        });

        //ПОПОЛНЕНИЕ СЧЕТА
        $router->group(['prefix' => '/payment', 'namespace' => 'Payment'], function (Router $router) {

            $router->post('/load', 'PaymentController@load');
            $router->post('/order', 'PaymentController@order');

        });

        //ПРОМО-КОДЫ
        $router->post('/promo/check', 'PromoController@check');

        //РАЗБАН
        $router->post('/unban', 'UnbanController@unban');

        //ЛОГИ
        $router->post('/logs/{type}', 'LogsController@getLogs')
            ->where('type', '[0-9]+');

        //УВЕДОМЛЕНИЯ (NOTIFICATIONS)
        $router->post('/notifications/load', 'NotificationsController@loadNotifications');
        $router->post('/notifications/load-last', 'NotificationsController@loadLastNotifications');
        $router->post('/notifications/read', 'NotificationsController@readNotifications');

        //ГОЛОСОВАНИЯ
        $router->post('/votes/load', 'VotesController@load');

        //РЕФЕРАЛЫ
        $router->post('/referal/load', 'ReferalController@load');

        //БАГ-РЕПОРТ
        $router->group(['prefix' => '/bugreport', 'namespace' => 'BugReport'], function (Router $router) {

            $router->post('/load', 'BugReportController@load');
            $router->post('/send/{server}', 'BugReportController@send')
                ->where('server', '[0-9]+');
            $router->post('/messages/load/{id}', 'MessagesController@load')
                ->where('id', '[0-9]+');
            $router->post('/messages/send/{id}', 'MessagesController@send')
                ->where('id', '[0-9]+');

        });

        //НАВЫКИ
        $router->group(['prefix' => '/skills'], function (Router $router) {

            $router->post('/load', 'SkillsController@load');
            $router->post('/up', 'SkillsController@skillUp');

        });

        //НАСТРОЙКИ
        $router->group(['prefix' => '/settings', 'namespace' => 'Settings'], function (Router $router) {

            $router->post('/load', 'SettingsController@load');

            $router->post('/google-check', 'TwoFactorAuthController@googleCheck');
            $router->post('/google-enable', 'TwoFactorAuthController@googleEnable');
            $router->post('/google-disable', 'TwoFactorAuthController@googleDisable');

        });

    });

});