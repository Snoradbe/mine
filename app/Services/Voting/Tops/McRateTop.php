<?php


namespace App\Services\Voting\Tops;


use App\Exceptions\Exception;

class McRateTop implements Top
{
    protected $secretKey;

    protected $rewards;

    protected $nickname;

    public function __construct(array $data)
    {
        $this->secretKey = $data['secret'];
        $this->rewards = $data['rewards'];
    }

    public function getName(): string
    {
        return 'mcrate';
    }

    public function init(array $post): void
    {
        if (!isset($post['nick']) || empty(trim($post['nick']))) {
            throw new Exception('Имя пользователя не введено!');
        }

        if (!isset($post['hash']) || empty(trim($post['hash']))) {
            throw new Exception('Нет подписи!');
        }

        $this->nickname = trim($post['nick']);
    }

    public function getUserName(): string
    {
        return $this->nickname;
    }

    public function getRewards(): array
    {
        return $this->rewards;
    }

    public function checkSign(array $post): bool
    {
        return $post['hash'] == md5(md5($post['nick'] . $this->secretKey . 'mcrate'));
    }

    public function error(string $message): void
    {
        throw new Exception($message);
    }

    public function success(): void
    {
        print 'OK';
    }
}