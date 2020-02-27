<?php


namespace App\Services\Forum;


use App\Entity\Forum\Member;
use App\Entity\Site\User;
use App\Repository\Forum\Member\DoctrineMemberRepository;

class ForumManager
{
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}

    public static function getMember(User $user): ?Member
    {
        return doctrine_connection(DoctrineMemberRepository::class, Member::class, 'forum')
            ->find($user->getName());
    }

    public static function updateMember(Member $member): void
    {
        doctrine_connection(DoctrineMemberRepository::class, Member::class, 'forum')
            ->update($member);
    }
}