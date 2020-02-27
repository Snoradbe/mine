<?php


namespace App\Services\Auth\Hasher;


class MD5Hasher implements Hasher
{
    public function hash(string $value): string
    {
        return md5($value);
    }

    public function check(string $value, string $hash): bool
    {
        return md5($value) === $hash;
    }

    public function checkFromSession(string $value, string $hash): bool
    {
        return md5($value) === $hash;
    }
}