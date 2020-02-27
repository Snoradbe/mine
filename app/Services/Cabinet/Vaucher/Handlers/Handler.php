<?php


namespace App\Services\Cabinet\Vaucher\Handlers;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;

interface Handler
{
    /**
     * @param string $post
     * @return mixed
     */
    public function getReward(string $post);

    /**
     * @param User $user
     * @param Vaucher $vaucher
     * @param string $message
     * @return array - ['amount' => 15, 'msg' => $message]
     */
    public function handle(User $user, Vaucher $vaucher, string $message): array;
}