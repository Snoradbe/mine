<?php


namespace App\Repository\Site\TopLastVotes;


use App\Entity\Site\TopLastVotes;
use App\Repository\DoctrineConstructor;

class DoctrineTopLastVotesRepository implements TopLastVotesRepository
{
    use DoctrineConstructor;
    
    public function create(TopLastVotes $topLastVotes): void
    {
        $this->em->persist($topLastVotes);
        $this->em->flush();
    }
}