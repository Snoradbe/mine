<?php


namespace App\Services\Voting\Tops;


use App\Exceptions\Exception;

class McTopTop implements Top
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
        return 'mctop';
    }

    public function init(array $post): void
    {
        if (!isset($post['nickname']) || empty(trim($post['nickname']))) {
            throw new Exception('Имя пользователя не введено!');
        }

        if (!isset($post['token']) || empty(trim($post['token']))) {
            throw new Exception('Нет подписи!');
        }

        $this->nickname = trim($post['nickname']);
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
        return $post['token'] == md5($post['nickname'] . $this->secretKey);
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
