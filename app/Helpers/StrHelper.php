<?php


namespace App\Helpers;


class StrHelper
{
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}


    public static function transformRussian(string $text): string
    {
        return str_replace(
            config('site.replace_alphabet.from', []),
            config('site.replace_alphabet.to', []),
            mb_strtolower($text, 'UTF-8')
        );
    }
}