<?php


namespace App\Services\Cabinet\Cloak;


class Validator
{
    private function baseScale(int $width, int $height): bool
    {
        $ratio = (int) $width / 64;

        return $width / $ratio === 64 && $height / $ratio === 32;
    }

    public function validate(int $width, int $height): bool
    {
        if ($this->baseScale($width, $height)) {
            return true;
        }

        $ratio = (int) $width / 17;

        return $width / $ratio === 22 && $height / $ratio === 17;
    }
}