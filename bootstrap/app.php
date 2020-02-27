<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

if (!function_exists('doctrine_connection')) {
    function doctrine_connection(string $repository, ?string $entity, string $connection)
    {
        $mr = app()->make(\Doctrine\Common\Persistence\ManagerRegistry::class);

        /* @var \Doctrine\ORM\EntityManager $em */
        $em = $mr->getManager($connection);

        if(is_null($entity)) {
            return new $repository($em);
        }

        return new $repository($em, $em->getRepository($entity));
    }
}

if (!function_exists('server_common_connection')) {
    function server_common_connection(string $repository, ?string $entity)
    {
        return doctrine_connection($repository, $entity, 'server_common');
    }
}

if (!function_exists('settings')) {
    function settings(string $key, ?string $castTo = null, $default = null)
    {
        /* @var \App\Services\Settings\Settings $settings */
        $settings = app()->make(\App\Services\Settings\Settings::class);
        $setting = $settings->get($key, $default);

        if ($setting instanceof \App\Entity\Site\Setting) {
            $setting = $setting->getValue($castTo);
        }

        return $setting;
    }
}

if (!function_exists('permission_middleware')) {
    /**
     * @param string $permissions список пермишенов, можно через ; без пробелов
     * @param bool $mpType
     * @param bool $prefixMethod
     * @return string
     */
    function permission_middleware(string $permissions, bool $mpType = false, bool $prefixMethod = false): string
    {
        $str = 'permissions:' . $permissions . ',' . ($mpType ? 'true' : 'false') . ',' . ($prefixMethod ? 'true' : 'false');

        return $str;
    }
}

if (!function_exists('permissions_middleware')) {
    function permissions_middleware(array $permissions, bool $mpType = false, bool $prefixMethod = false): string
    {
        $str = 'permissions:' . implode(';', $permissions) . ',' . ($mpType ? 'true' : 'false') . ',' . ($prefixMethod ? 'true' : 'false');

        return $str;
    }
}

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
