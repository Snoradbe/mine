<?php


namespace App\Http\Middleware;


use App\Exceptions\PermissionDeniedException;
use Illuminate\Http\Request;

class CheckPermissions
{
    /**
     * Проверка доступа
     * Если несколько пермишенов, то будет искать хотябы 1
     * Пока что несколько пермишенов можно указывать только для админских роутов
     *
     * @param Request $request
     * @param \Closure $next
     * @param string $permissions пермишнен, например mp.applications.view можно через ; без пробелов
     * @param bool $mpType если true, то проверяются только админские права и редиректит тоже в админ панель
     * @param bool $prefixMethod если true, то проверяется 1 любое право начинающееся с префикса
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, string $permissions, bool $mpType = false, bool $prefixMethod = false)
    {
        if (is_null(\App\Services\Auth\Auth::getUser())) {
            return redirect()->route($mpType ? 'admin' : 'index');
        }

        $permissions = explode(';', $permissions);

        if (count($permissions) == 1) {
            $permission = $permissions[0];

            if ($prefixMethod) {
                $hasPermission = $mpType
                    ? \App\Services\Auth\Auth::getUser()->permissions()->containsMPPermissionPrefix($permission)
                    : \App\Services\Auth\Auth::getUser()->permissions()->containsPermissionPrefix($permission);
            } else {
                $hasPermission = $mpType
                    ? \App\Services\Auth\Auth::getUser()->permissions()->hasMPPermission($permission)
                    : \App\Services\Auth\Auth::getUser()->permissions()->hasPermission($permission);
            }

            if (!$hasPermission) {
                return redirect()->route($mpType ? 'admin' : 'index')->withErrors(PermissionDeniedException::MSG);
            }
        } else {
            if (!\App\Services\Auth\Auth::getUser()->permissions()->hasMPAnyPermissions($permissions)) {
                return redirect()->route($mpType ? 'admin' : 'index')->withErrors(PermissionDeniedException::MSG);
            }
        }

        return $next($request);
    }
}