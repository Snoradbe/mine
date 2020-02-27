<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\Exception;
use App\Handlers\Api\Vote\VoteHandler;
use App\Http\Controllers\Controller;

class VoteController extends Controller
{
    public function vote(VoteHandler $handler, string $top): void
    {
        try {
            $handler->handle($top);
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }
}