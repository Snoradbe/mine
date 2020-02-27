<?php

namespace App\Exceptions\Rcon;

use App\Exceptions\Exception;
use Throwable;

class ServerDoesNotExistsException extends Exception
{
    public function __construct($server, $code = 0, Throwable $previous = null)
    {
        $message = "Server with name \"{$server}\" does not exists in the server pool";

        parent::__construct($message, $code, $previous);
    }
}
