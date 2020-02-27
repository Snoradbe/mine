<?php


namespace App\Helpers;


class EntityHelper
{
    private function __construct(){}

    public static function jsonStringToArray(?string $string): array
    {
        return empty($string) ? [] : (array) json_decode($string, true);
    }
}