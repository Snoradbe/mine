<?php


namespace App\Repository\Forum\Member;


use App\Entity\Forum\Member;

interface MemberRepository
{
    public function find(string $name): ?Member;

    public function update(Member $member): void;
}