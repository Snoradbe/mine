<?php


namespace App\Repository\Site\TopLastVotes;


use App\Entity\Site\TopLastVotes;

interface TopLastVotesRepository
{
    public function create(TopLastVotes $topLastVotes): void;
}