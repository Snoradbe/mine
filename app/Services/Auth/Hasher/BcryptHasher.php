<?php


namespace App\Services\Auth\Hasher;


class BcryptHasher implements Hasher
{
    public function hash(string $value): string
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    public function check(string $value, string $hash): bool
    {
		//23d27ea56446944d219f96f1f8301a78
		//289af5c8c97052964394bab39ccf4c6b
		dd(md5('23d27ea56446944d219f96f1f8301a78'));
        return password_verify($value, $hash);
    }

    public function checkFromSession(string $value, string $hash): bool
    {
		//23d27ea56446944d219f96f1f8301a78
		//289af5c8c97052964394bab39ccf4c6b
		dd(md5('23d27ea56446944d219f96f1f8301a78'));
        return password_verify($value, $hash);
    }
}