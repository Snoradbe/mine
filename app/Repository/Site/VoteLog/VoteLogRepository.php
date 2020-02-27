<?php


namespace App\Repository\Site\VoteLog;


use App\Entity\Site\User;
use App\Entity\Site\VoteLog;

interface VoteLogRepository
{
    public function getLatest(): array;

    /**
     * @param User $user
     * @return VoteLog[]
     */
    public function getUserToday(User $user): array;

    public function getCountToday(User $user): int;

    public function getUserLast(User $user): ?VoteLog;

    public function create(VoteLog $voteLog): void;
}