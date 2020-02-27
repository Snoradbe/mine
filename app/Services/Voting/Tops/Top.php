<?php


namespace App\Services\Voting\Tops;


use App\Exceptions\Exception;

interface Top
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param array $post
     * @throws Exception
     */
    public function init(array $post): void;

    /**
     * @return string
     */
    public function getUserName(): string;

    /**
     * @return array
     */
    public function getRewards(): array;

    /**
     * @param array $post
     * @return bool
     */
    public function checkSign(array $post): bool;

    /**
     * @param string $message
     */
    public function error(string $message): void;

    /**
     * @return void
     */
    public function success(): void;
}