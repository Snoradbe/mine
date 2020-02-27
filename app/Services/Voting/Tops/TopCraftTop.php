<?php


namespace App\Services\Voting\Tops;


use App\Exceptions\Exception;

class TopCraftTop implements Top
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
        return 'topcraft';
    }

    public function init(array $post): void
    {
        if (!isset($post['username']) || empty(trim($post['username']))) {
            throw new Exception('Имя пользователя не введено!');
        }

        if (!isset($post['signature']) || empty(trim($post['signature']))) {
            throw new Exception('Нет подписи!');
        }

        if (!isset($post['timestamp']) || empty(trim($post['timestamp']))) {
            throw new Exception('Нет даты!');
        }

        $this->nickname = trim($post['username']);
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
        return $post['signature'] == sha1($post['username'] . $post['timestamp'] . $this->secretKey);
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