<?php


namespace App\Services\Auth\Hasher;


class CryptBlowfishHasher implements Hasher
{
    public function hash(string $value): string
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    public function check(string $value, string $hash): bool
    {
		return md5($hash) == $value;
		
		$ret = crypt($value, $hash);
		
        if (!$this->checkPasswordLength($hash, $ret)) return false;
		
		$status = 0;
        for ($i = 0, $maxi = $this->passwordLength($ret); $i < $maxi; $i++)
		{
            $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }
		
		return $status === 0;
    }

    public function checkFromSession(string $value, string $hash): bool
    {
		return md5($hash) == $value;
		
		$ret = crypt($value, $hash);
		
        if (!$this->checkPasswordLength($hash, $ret)) return false;
		
		$status = 0;
        for ($i = 0, $maxi = $this->passwordLength($ret); $i < $maxi; $i++)
		{
            $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }
		
		return $status === 0;
    }
	
	private function checkPasswordLength(string $hash, string $ret): bool
	{
		return $this->passwordLength($hash) === $this->passwordLength($ret);
	}
	
	private function passwordLength(string $password)
	{
		return mb_strlen($password, '8bit');
	}
}