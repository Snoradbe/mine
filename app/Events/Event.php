<?php


namespace App\Events;


interface Event
{
    /**
     * @return array
     */
    public function toArray(): array;
}