<?php


namespace App\Http\Controllers\Client\Settings;


use App\Http\Controllers\Controller;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;

class SettingsController extends Controller
{
    public function load()
    {
        $user = Auth::getUser();

        return new JsonResponse([
            'g2fa' => $user->hasG2fa()
        ]);
    }
}