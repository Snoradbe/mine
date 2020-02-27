<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionDeniedException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    protected function redirectNoPermissions(string $route = 'index')
    {
        return redirect()->route($route)->withErrors(PermissionDeniedException::MSG);
    }

    protected function redirectNoPermissionsToMP()
    {
        return $this->redirectNoPermissions('admin');
    }
}
