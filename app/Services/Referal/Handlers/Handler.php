<?php


namespace App\Services\Referal\Handlers;


use App\Entity\Site\User;

interface Handler
{
    /**
     * @param User $user
     * @param string $type
     * @param array $data
     * @return mixed
     */
    public function handle(User $user, string $type, array $data);
}