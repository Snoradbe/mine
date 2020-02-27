<?php


namespace App\Services\Voting\RewardHandlers;


use App\Entity\Site\User;

interface RewardHandler
{
    public function handle(User $user, array $data);
}