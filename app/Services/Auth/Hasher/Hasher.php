<?php


namespace App\Services\Auth\Hasher;


interface Hasher
{
    public function hash(string $value): string;

    public function check(string $value, string $hash): bool;
	
	public function checkFromSession(string $value, string $hash): bool;
}