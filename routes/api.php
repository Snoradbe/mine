<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* @var \Illuminate\Routing\Router $router */

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/*$router->get('/user/test', function (\App\Repository\Site\User\UserRepository $userRepository) {

    $user = $userRepository->find(2);

    event(new \App\Events\Api\User\LevelUpEvent($user, 1, 2));

});*/

$router->get('/test1', function () {
    return 'hi1';
});

$router->get('/payment/{payer}', 'Api\PaymentController@pay')
    ->where('payer', '[a-z0-9_]+');

$router->get('/vote/{top}', 'Api\VoteController@vote')
    ->where('top', '[a-z0-9_]+');

$router->group(['prefix' => '/game-api'], function (\Illuminate\Routing\Router $router) {
    $router->group(['prefix' => '/s{server}', 'where' => ['server' => '[0-9]+']], function (\Illuminate\Routing\Router $router) {
        $router->get('/playtime', 'Api\PlayTimeController@send'); //TODO: надо бы сделать проверку IP или ТОКЕНА, чтоб левые не слали
    });

    $router->any('/launcher/auth', 'Api\Launcher\AuthProviderController@auth');
    $router->any('/launcher/check2fa', 'Api\Launcher\AuthProviderController@a2fa');
});
