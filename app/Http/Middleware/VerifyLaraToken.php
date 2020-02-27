<?php


namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

class VerifyLaraToken
{
    public function handle(Request $request, Closure $next)
    {
        $user = \App\Services\Auth\Auth::getUser();
        $token = $request->input('_lara_token') ?: $request->header('X-CSRF-TOKEN');

        if (empty($token) || is_null($user) || $user->getLaraToken() != $token) {
            //throw new \Exception('Token is not valid!', 419);
            throw new TokenMismatchException();
        }

        return $next($request);
    }
}