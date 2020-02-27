<?php


namespace App\Services\Cabinet\Skin;


class Validator
{
    public function validate(int $width, int $height): bool
    {
        $ratio = (int) $width / 64;

        return $width / $ratio === 64 && $height / $ratio === 32;
    }
}