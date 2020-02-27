<?php


namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class ControlOrigin
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', config('site.allowed_url'))
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, X-CSRF-TOKEN, X-Requested-With, Set-Cookie');
        //->header('Access-Control-Allow-Origin', 'http://test.loc');
    }
}