<?php

namespace App\Services\Game\Rcon;


use App\Exceptions\Exception;

interface Connection
{
    /**
     * Send command.
     *
     * @param string $command
     * @param bool   $getFullResponse
     *
     * @throws Exception
     *
     * @return string|array|null
     */
    public function send($command, $getFullResponse = false);

    /**
     * Returns last response or null.
     *
     * @return mixed
     */
    public function last();

    /**
     * Disconnect from RCON.
     */
    public function disconnect();
}
