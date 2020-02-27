<?php


namespace App\Exceptions;


use Throwable;

class PermissionDeniedException extends Exception
{
    public const MSG = 'У вас недостаточно прав для этого!';

    public function __construct()
    {
        parent::__construct(static::MSG);
    }
}