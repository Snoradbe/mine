<?php


namespace App\Http\Middleware;


use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class Auth
{
    public const LOGGED = 'logged';
    public const GUEST = 'guest';
    public const IN_TEAM = 'in_team';

    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(Request $request, \Closure $next, string $mode)
    {
        //Фейковый вход, если нужно для тестов
        //if (!empty($request->cookie('go_fake_user'))) {
        //    /* @var \App\Entity\Site\User $user */
        //    $user = app()->make(\App\Repository\Site\User\UserRepository::class)->findByName($request->cookie('go_fake_user'));
        //    \App\Services\Auth\Auth::setUser($user);
        //    return $next($request);
        // }

        $check = $this->authService->check();

        if ($mode == static::LOGGED && !$check) {
            return redirect('/no-logged');
        }

        if ($mode == static::GUEST && $check) {
            return redirect('/logged');
        }

        if ($mode == static::IN_TEAM && (!$check || !\App\Services\Auth\Auth::getUser()->inTeam())) {
            return redirect('/no-team');
        }

        return $next($request);
    }
}