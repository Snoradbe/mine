<?php

use App\Http\Middleware\Auth;
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

/*$router->get('/perms', function (\App\Repository\Site\Server\ServerRepository $serverRepository) {

    $server = $serverRepository->find(1);

    $m = microtime(true);

    $perm = \App\Helpers\PermissionsHelper::MP_APPLICATIONS;

    dd([
        \App\Helpers\PermissionsHelper::containsPermissionPrefix($perm, $server),
        microtime(true) - $m
    ]);

})->middleware('auth:logged');

$router->get('/aa', function (\App\Repository\Site\Server\ServerRepository $serverRepository) {

    $server = $serverRepository->find(1);
    $user = \App\Services\Auth\Auth::getUser();
    $manager = \App\Services\Game\GameMoney\GameMoneyManagerFactory::getManager($server);

    //$manager->setMoney($user, 888);

    dd($manager->getMoneyEntity(\App\Services\Auth\Auth::getUser())->getMoney());

})->middleware('auth:logged');*/

$router->group(['domain' => 'moder.realmine.net', 'namespace' => 'Admin'], function (Router $router) {
    include_once __DIR__ . '/admin.php';
});

$router->get('/no-logged', function () {
    return 'Авторизуйтесь!';
});

$router->get('/install/upgrade/{version}/{reset?}', function (string $version, ?bool $reset = false) {

    $controller = \App\Http\Controllers\Install\InstallController::getController($version);

    if (class_exists($controller)) {

        if ($reset) {
            $result = \App\Http\Controllers\Install\InstallController::reset($version);
        } else {
            $result = app()->make($controller)->install();
        }

        if ($result == 'ok') {
            return new \Illuminate\Http\Response('Апгрейд выполнен');
        } else {
            return new \Illuminate\Http\Response($result);
        }
    } else {
        return new \Illuminate\Http\Response('Апгрейд не найден!');
    }

})->where('version', '[0-9]+')->where('reset', '(true|false)');

$router->get('mon', function () {
    $query = new \App\Services\Game\MineQuery\Query();
    //$query = new \App\Services\Game\MineQuery\Ping('88.99.160.90', 22555);
    $query->Connect('88.99.160.90', 22555);
    //$query->Connect();
    dd($query->GetInfo());
});

/*$router->group(['prefix' => '/install', 'namespace' => 'Install'], function (Router $router) {
    $version = request()->get('version_date');
    if (empty(trim($version))) {
        return;
    }

    $controller = \App\Http\Controllers\Install\InstallController::getController($version);

    if (!class_exists($controller)) {
        return;
    }

    $router->get('/', "Install{$version}@install");
});*/