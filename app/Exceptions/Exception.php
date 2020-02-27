<?php


namespace App\Exceptions;


use App\Services\Response\JsonResponse;
use Illuminate\Http\RedirectResponse;

class Exception extends \Exception
{
    public function toJsonResponse(): JsonResponse
    {
        return new JsonResponse(['msg' => $this->getMessage()], 500);
    }

    public function redirectBack(): RedirectResponse
    {
        return redirect()->back()->withErrors($this->getMessage());
    }
}