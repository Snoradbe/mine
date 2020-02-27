<?php


namespace App\Services\Auth\Session\Driver;


interface Driver
{
    public function getUserId(): ?int;

    public function getHashedPassword(): ?string;

    public function forget(): void;
}